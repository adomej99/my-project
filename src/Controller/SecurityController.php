<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class SecurityController extends AbstractController
{
    private EntityManagerInterface $entityManager;


    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        $this->entityManager = $entityManager;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, JWTTokenManagerInterface $jwtManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'];
        $password = $data['password'];

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            throw new BadCredentialsException('Invalid username or password');
        }

        if (!password_verify($password, $user->getPassword())) {
            throw new BadCredentialsException('Invalid username or password');
        }

        $token = $jwtManager->create($user);

        return $this->json(['token' => $token]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, JWTTokenManagerInterface $jwtManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();

        $content = json_decode($request->getContent(), true);

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $content['password']
            )
        );

        $user->setUsername($content['username']);
        $user->setEmail($content['email']);
        $user->setRegion($content['region']);
        $user->setNumber($content['phone_number']);
        $user->setCity($content['city']);
        $user->setOtherContacts($content['other_contacts']);

        $user->setRoles(['ROLE_USER']);

        $user->setIsActive(1);

        $check = $this->entityManager->getRepository(User::class)->findBy(['username' => $user->getUsername()]);

        if(!empty($check))
        {
            return $this->json(['error' => 'User with this username already exists']);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $token = $jwtManager->create($user);

        return $this->json(['token' => $token]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
