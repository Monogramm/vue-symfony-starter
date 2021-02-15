<?php

namespace App\Security;

use App\Handler\Security\AuthenticationSuccessHandler;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LoginFormAuthenticator extends AbstractGuardAuthenticator
{
    public const NO_PASSWORD = '!$';

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;
    /** @var AuthenticationSuccessHandler */
    private $successHandler;
    /** @var UserRepository */
    private $userRepository;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        AuthenticationSuccessHandler $successHandler
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->successHandler = $successHandler;
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request)
    {
        return 'api_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        return json_decode($request->getContent(), true);
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $this->userRepository->findOneBy(['username' => $credentials['username']]);

        if (!$user) {
            $user = $this->userRepository->findOneBy(['email' => $credentials['username']]);
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!$user->isEnabled()) {
            return false;
        }

        if ($credentials['password'] === self::NO_PASSWORD) {
            return false;
        }

        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return $this->successHandler->onAuthenticationSuccess($request, $token);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
    }

    public function supportsRememberMe()
    {
    }
}
