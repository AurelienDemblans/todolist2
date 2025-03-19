<?php

namespace App\Tests\Functionnal\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private string $routeLoginName = 'login';

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function testLoginPageNotLogged()
    {
        $urlGenerator = static::getContainer()->get(RouterInterface::class);

        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate($this->routeLoginName));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('/login', $this->client->getRequest()->getPathInfo());
        $this->assertSame(
            1,
            $crawler->filter('input[id=password]')->count()
        );
        $this->assertSame(
            1,
            $crawler->filter('input[id=username]')->count()
        );
    }

    public function testSubmitLogin()
    {
        $urlGenerator = static::getContainer()->get(RouterInterface::class);

        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate($this->routeLoginName));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('/login', $this->client->getRequest()->getPathInfo());
        $this->assertSame(
            1,
            $crawler->filter('input[id=password]')->count()
        );
        $this->assertSame(
            1,
            $crawler->filter('input[id=username]')->count()
        );
        $form = $crawler->selectButton('Se connecter')->form();

        $form['_username'] = 'john@test.com';
        $form['_password'] = '12345';

        $crawler = $this->client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('/', $this->client->getRequest()->getPathInfo());

        $this->assertEquals('john@test.com', static::getContainer()->get(Security::class)->getUser()->getEmail());
    }
}
