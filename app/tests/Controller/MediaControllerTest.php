<?php

namespace App\Tests\Controller;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class MediaControllerTest extends WebTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager = null;
    /**
     * @var UserRepository
     */
    private $userRepository = null;

    /**
     * @var KernelBrowser
     */
    private $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function testSecuredHello()
    {
        /**
         * @var ApiToken
         */
        $apiToken = $this->logIn();
        $this->client->request('GET', '/api/admin/media', [], [], [
            'Authorization' => 'Bearer ' . $apiToken->getToken(),
        ]);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
        // FIXME Find out how to authenticate an API Token in tests
        // https://symfony.com/doc/4.4/testing/http_authentication.html#creating-the-authentication-token
        //$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        //$responseContent = $this->client->getResponse()->getContent();
        //$content = json_decode($responseContent);
        //$this->assertSame(1, $content['total']);
    }

    private function logIn($username = 'username')
    {
        $session = self::$container->get('session');

        $user = $this->userRepository->findOneBy(['username' => $username]);

        $firewallName = 'main';
        // if you don't define multiple connected firewalls, the context defaults to the firewall name
        // See https://symfony.com/doc/current/reference/configuration/security.html#firewall-context
        $firewallContext = 'main';

        // TODO Generate a valid JWT token?
        $jwt = 'dummy';

        // Generate an API Token
        $token = new ApiToken();
        $token->setToken('dummy');
        $token->setExpiredAt(Carbon::now()->addDays(7));
        $user->addToken($token);
        $this->entityManager->persist($user);
        $this->entityManager->persist($token);
        $this->entityManager->flush();

        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        return $token;
    }
}
