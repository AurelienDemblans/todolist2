<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class DefaultControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private UserRepository|null $userRepository = null;
    private array $userEmails = ['admin' => "john@test.com", 'user' => "marc@test.com"];

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    public function testHomePageNotLogged()
    {
        $urlGenerator = static::getContainer()->get(RouterInterface::class);

        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('homepage'));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('/login', $this->client->getRequest()->getPathInfo());
    }

    public function testHomePageAsRoleUser()
    {
        $this->logAsRoleUser();

        $urlGenerator = static::getContainer()->get(RouterInterface::class);

        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('homepage'));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('/', $this->client->getRequest()->getPathInfo());
        $this->assertStringContainsString(
            "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !",
            $crawler->filter('h1')->text()
        );
    }

    public function testHomePageAsRoleAdmin()
    {
        $this->logAsRoleAdmin();

        $urlGenerator = static::getContainer()->get(RouterInterface::class);

        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('homepage'));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('/', $this->client->getRequest()->getPathInfo());
        $this->assertStringContainsString(
            "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !",
            $crawler->filter('h1')->text()
        );
    }

    private function logAsRoleUser(): void
    {
        $user = $this->userRepository->findOneByEmail(
            $this->userEmails['user']
        );

        $this->client->loginUser($user);
    }

    private function logAsRoleAdmin(): void
    {
        $user = $this->userRepository->findOneByEmail(
            $this->userEmails['admin']
        );

        $this->client->loginUser($user);
    }
}
