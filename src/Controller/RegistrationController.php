<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
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

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        // do anything else you need here, like send an email

        return $this->json(['message' => 'User created successfully']);
    }
}
