<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $authenticationManager;
    private $tokenStorage;

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, PersistenceManagerRegistry $doctrine, JWTTokenManagerInterface $jwtManager): Response
    {

//        $session->clear();
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

//        $sessionData = [
//            'user_id' => $user->getId(),
//            'username' => $user->getUsername(),
//            'roles' => $user->getRoles()
//            // ... any other user data you want to store in the session
//        ];

//        $session->set('user_id', $user->getId());

        $this->requestStack->getSession()->set('user_id', $user->getId());

//        $session->set('user', $sessionData);

        $token = $jwtManager->create($user);


        // TODO: Return the session data to the Vue.js project
        return $this->json(['token' => $token]);
    }

    public function loginTest(Request $request, PersistenceManagerRegistry $doctrine): JsonResponse
    {
        // get the user credentials from the request body
        $credentials = json_decode($request->getContent(), true);

        $data = json_decode($request->getContent(), true);

        $username = $data['username'];
        $password = $data['password'];

        // use Symfony's built-in authentication system to authenticate the user
        $userProvider = $doctrine->getManager()->getRepository(User::class)->findOneBy(['username' => $username]);
//        $userProvider = $this->getDoctrine()->getRepository(User::class);
        $token = new UsernamePasswordToken($userProvider, 'main', $userProvider->getRoles());
        try {
            $authenticatedToken = $this->authenticationManager->authenticate($token);
        } catch (Exception $exception) {
            return new JsonResponse(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }
        $this->tokenStorage->setToken($authenticatedToken);


        // create a session for the authenticated user
        $session = $request->getSession();
        $session->set('_security_main', serialize($authenticatedToken));

        return new JsonResponse('', Response::HTTP_OK);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
