<?php
//
//// src/Security/JwtAuthenticator.php
//
//namespace App\Security;
//
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\Security\Core\User\UserProviderInterface;
//use Symfony\Component\Security\Core\Exception\AuthenticationException;
//use Symfony\Component\Security\Core\User\UserInterface;
//use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
//use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
//use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
//use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
//use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
//
//class JwtAuthenticator extends AbstractAuthenticator
//{
//    private $jwtManager;
//
//    public function __construct(JWTTokenManagerInterface $jwtManager)
//    {
//        $this->jwtManager = $jwtManager;
//    }
//
//    public function supports(Request $request): bool
//    {
//        return $request->headers->has('Authorization');
//    }
//
//    public function getCredentials(Request $request)
//    {
//        $token = str_replace('Bearer ', '', $request->headers->get('Authorization'));
//
//        if (!$token) {
//            throw new AuthenticationException('Missing token');
//        }
//
//        try {
//            $payload = $this->jwtManager->decode($token);
//        } catch (InvalidTokenException $e) {
//            throw new AuthenticationException('Invalid token');
//        }
//
//        return $payload;
//    }
//
//    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
//    {
//        $username = $credentials['username'];
//
//        if (!$username) {
//            throw new AuthenticationException('Missing username');
//        }
//
//        return $userProvider->loadUserByUsername($username);
//    }
//
//    public function checkCredentials($credentials, UserInterface $user): bool
//    {
//        // Check if the user is allowed to access the requested resource
//        // ...
//
//        return true;
//    }
//
//    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
//    {
//        return new JsonResponse([
//            'message' => 'Authentication failed',
//        ], JsonResponse::HTTP_UNAUTHORIZED);
//    }
//
//    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?TokenInterface
//    {
//        return $token;
//    }
//
//    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
//    {
//        return new JsonResponse([
//            'message' => 'Authentication required',
//        ], JsonResponse::HTTP_UNAUTHORIZED);
//    }
//
//    public function supportsRememberMe(): bool
//    {
//        return false;
//    }
//
//    public function createAuthenticatedToken(UserInterface $user, string $providerKey): TokenInterface
//    {
//        return new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
//    }
//
//    public function supportsToken(TokenInterface $token, string $providerKey): bool
//    {
//        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
//    }
//
//    public function createToken(Request $request, string $username, string $password, string $providerKey): TokenInterface
//    {
//        return new UsernamePasswordToken($username, $password, $providerKey);
//    }
//
//    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
//    {
//        return new JsonResponse([
//            'message' => $exception->getMessage(),
//        ],
//
