<?php

namespace App\Security;

use App\Repository\ApiTokenRepository;
use Carbon\Carbon;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiTokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var ApiTokenRepository
     */
    private $tokenRepository;

    public function __construct(
        ApiTokenRepository $tokenRepository
    ) {
        $this->tokenRepository = $tokenRepository;
    }

    public function supports(Request $request)
    {
        return $request->headers->has('Authorization');
    }

    public function getCredentials(Request $request)
    {
        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'Authorization'
        );

        $token = $extractor->extract($request);

        if (!$token) {
            return;
        }

        return $token;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = $this->tokenRepository->findOneBy([
            'token' => $credentials
        ]);

        if (!$token || $token->isNowExpired()) {
            return;
        }

        return $token->getUser();
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!$user->isEnabled()) {
            return false;
        }

        return true;
    }

    /**
     * @return void
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
    }

    /**
     * @return void
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return void
     */
    public function supportsRememberMe()
    {
    }
}
