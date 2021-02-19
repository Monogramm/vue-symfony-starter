<?php


namespace App\Security;

use App\Entity\Parameter;
use App\Entity\User;
use App\Handler\Security\AuthenticationSuccessHandler;
use App\Repository\ParameterRepository;
use App\Repository\UserRepository;
use App\Service\Ldap\Client;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LdapAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var AuthenticationSuccessHandler
     */
    private $successHandler;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var Client
     */
    private $ldap;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var array
     */
    private $ldapConfig;
    /**
     * @var ParameterRepository
     */
    private $parameterRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Client $ldap,
        UserRepository $userRepository,
        AuthenticationSuccessHandler $successHandler,
        EntityManagerInterface $em,
        RouterInterface $router,
        ParameterRepository $parameterRepository,
        array $ldapConfig,
        LoggerInterface $logger
    ) {
        $this->ldap = $ldap;
        $this->successHandler = $successHandler;
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->router = $router;
        $this->parameterRepository = $parameterRepository;
        $this->ldapConfig = $ldapConfig;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
    }

    public function supports(Request $request)
    {
        return 'api_ldap_auth' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        return json_decode($request->getContent(), true);
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!$this->ldapConfig['enabled']) {
            return null;
        }

        $username = $credentials['username'];
        $password = $credentials['password'];
        try {
            $entry = $this->ldap->check($username, $password);
        } catch (\Throwable $e) {
            $this->logger->debug('Failed to check against LDAP. Error message: %s'.$e->getMessage());
            return null;
        }

        if (!isset(
            $entry->getAttribute($this->ldapConfig['mail_key'])[0],
            $entry->getAttribute($this->ldapConfig['uid_key'])[0]
        )) {
            $this->logger->debug('Failed to authenticate user against LDAP.');
            return null;
        }

        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            $role = $this->getUserDefaultRole();
            $user = (new User())
                ->setEmail($entry->getAttribute($this->ldapConfig['mail_key'])[0])
                ->setUsername($entry->getAttribute($this->ldapConfig['uid_key'])[0])
                ->setPassword(LoginFormAuthenticator::NO_PASSWORD)
                ->verify();
            if ($role) {
                $user->setRoles([$role]);
            }
            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return $this->successHandler->onAuthenticationSuccess($request, $token);
    }

    /**
     * @return void
     */
    public function supportsRememberMe()
    {
    }

    private function getUserDefaultRole(): ?string
    {
        /**
         * @var Parameter|null $parameter
         */
        $parameter = $this->parameterRepository->findByName('LDAP_USER_DEFAULT_ROLE');

        if (!$parameter) {
            return null;
        }

        return $parameter->getValue();
    }
}
