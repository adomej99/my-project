<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, SessionInterface $session, PersistenceManagerRegistry $doctrine): Response
    {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'];
        $password = $data['password'];

//        return $this->json(['session' => $sessionData]);

        // TODO: Authenticate the user with the given username and password
        // ...

        $user = $doctrine->getManager()->getRepository(User::class)->findOneBy(['username' => $username]);

//        return $this->json(['session' => $user]);


        if (!$user) {
            throw new BadCredentialsException('Invalid username or password');
        }

        if (!password_verify($password, $user->getPassword())) {
            throw new BadCredentialsException('Invalid username or password');
        }

        // TODO: Create a new session for the authenticated user
        // ...

        $sessionData = [
            'user_id' => $user->getId(),
            'username' => $user->getUsername(),
            // ... any other user data you want to store in the session
        ];

        $session->set('user', $sessionData);

        // TODO: Return the session data to the Vue.js project
        return $this->json(['session' => $sessionData]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
