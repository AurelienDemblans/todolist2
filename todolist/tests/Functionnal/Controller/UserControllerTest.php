<?php

namespace App\Tests\Functionnal\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private UserRepository|null $userRepository = null;
    private object|null $urlGenerator = null;
    private array $userEmails = ['admin' => "john@test.com", 'user' => "marc@test.com"];
    private string $listRouteName = "user_list";

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();

        $this->userRepository = static::getContainer()->get(UserRepository::class);

        $this->urlGenerator = static::getContainer()->get(RouterInterface::class);
    }

    public function testNotLogged()
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->listRouteName));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('/login', $this->client->getRequest()->getPathInfo());
    }

    public function testListPageAsRoleUser()
    {
        $this->logAsRoleUser();

        $urlGenerator = static::getContainer()->get(RouterInterface::class);

        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate($this->listRouteName));
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.container h1', 'Liste des utilisateurs');
        $this->assertSelectorTextContains('td', 'John');
        $this->assertSelectorTextContains('tr td:nth-child(3)', 'john@test.com');
        $this->assertCount(
            2,
            $crawler->filter('tr:first-child td'),
            "Il y'a plus de 2 cellule par lignes."
        );
    }

    public function testListPageAsRoleAdmin()
    {
        $this->logAsRoleAdmin();

        $urlGenerator = static::getContainer()->get(RouterInterface::class);

        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate($this->listRouteName));
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.container h1', 'Liste des utilisateurs');
        $this->assertSelectorTextContains('td', 'John');
        $this->assertSelectorTextContains('tr td:nth-child(3)', 'john@test.com');
        $this->assertSelectorTextContains('tr td:nth-child(4)', 'Edit');
        $this->assertSelectorExists('tr td:nth-child(4) a[href="/users/1/edit"]');
        $this->assertCount(
            3,
            $crawler->filter('tr:first-child td'),
            "Il y'a plus de 2 cellule par lignes."
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
