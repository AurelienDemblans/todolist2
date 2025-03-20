<?php

namespace App\Tests\Functionnal\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class UserControllerTest extends WebTestCase
{
    private User|null $userRoleAdmin = null;
    private User|null $userRoleUser = null;
    private KernelBrowser|null $client = null;
    private UserRepository|null $userRepository = null;
    private object|null $urlGenerator = null;
    private array $userEmails = ['admin' => "john@test.com", 'user' => "marc@test.com"];
    private string $listRouteName = "user_list";
    private string $editRouteName = 'user_edit';
    private string $createRouteName = 'user_create';

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();

        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->userRoleAdmin = $this->userRepository->findOneByEmail(
            'john@test.com'
        );
        $this->userRoleUser = $this->userRepository->findOneByEmail(
            'marc@test.com'
        );

        $this->urlGenerator = static::getContainer()->get(RouterInterface::class);
    }

    public function testListPageNotLogged()
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->listRouteName));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('/login', $this->client->getRequest()->getPathInfo());
    }

    public function testListPageAsRoleUser()
    {
        $this->logAsRoleUser();

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->listRouteName));
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

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->listRouteName));
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

    public function testEditPageNotLogged()
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->editRouteName, ['id' => 1]));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('/login', $this->client->getRequest()->getPathInfo());
    }

    public function testEditPageAsRoleUser()
    {
        $this->logAsRoleUser();

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->editRouteName, ['id' => $this->userRoleUser->getId()]));

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testEditPageAsRoleAdmin()
    {
        $userToEdit = $this->userRoleAdmin;
        $this->logAsRoleAdmin();

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->editRouteName, ['id' => $this->userRoleAdmin->getId()]));

        $this->assertSelectorTextContains('h1', 'Modifier');
        $this->assertSelectorTextContains('h1 strong', $userToEdit->getUsername());

        $expectedFields = [
            'user[username]',
            'user[email]',
            'user[password][first]',
            'user[password][second]',
            'user[role]'
        ];

        $form = $crawler->selectButton('Modifier')->form();

        foreach ($expectedFields as $fieldName) {
            $this->assertTrue(
                $form->has($fieldName),
                "Le champ '$fieldName' est manquant dans le formulaire"
            );
        }
    }

    public function testSubmitEditFormAsAdmin()
    {
        $userToEdit = $this->userRoleUser;
        $this->logAsRoleAdmin();

        $crawler = $this->client->request(Request::METHOD_POST, $this->urlGenerator->generate($this->editRouteName, ['id' => $this->userRoleUser->getId()]));
        $form = $crawler->selectButton('Modifier')->form();

        $form['user[username]'] = 'JohnTest';
        $form['user[email]'] = 'testtest@test.com';
        $form['user[password][first]'] = '123456';
        $form['user[password][second]'] = '123456';
        $form['user[role]'] = 'ROLE_USER';

        $crawler = $this->client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('/users', $this->client->getRequest()->getPathInfo());
        $this->assertStringContainsString(
            "L'utilisateur a bien été modifié",
            $crawler->filter('.alert.alert-success')->text()
        );
    }

    public function testCreatePageNotLogged()
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->createRouteName));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('/login', $this->client->getRequest()->getPathInfo());
    }

    public function testCreatePageAsRoleUser()
    {
        $this->logAsRoleUser();

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->createRouteName));

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreatePageAsRoleAdmin()
    {
        $this->logAsRoleAdmin();

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->createRouteName));

        $this->assertSelectorTextContains('h1', 'Créer un utilisateur');

        $expectedFields = [
            'user[username]',
            'user[email]',
            'user[password][first]',
            'user[password][second]',
            'user[role]'
        ];

        $form = $crawler->selectButton('Ajouter')->form();

        foreach ($expectedFields as $fieldName) {
            $this->assertTrue(
                $form->has($fieldName),
                "Le champ '$fieldName' est manquant dans le formulaire"
            );
        }
    }

    public function testSubmitCreateAsRoleAdmin()
    {
        $this->logAsRoleAdmin();

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->createRouteName));
        $form = $crawler->selectButton('Ajouter')->form();

        $this->assertSelectorTextContains('h1', 'Créer un utilisateur');

        $form['user[username]'] = 'test';
        $form['user[email]'] = 'test@test.com';
        $form['user[password][first]'] = '123456';
        $form['user[password][second]'] = '123456';
        $form['user[role]'] = 'ROLE_USER';

        $crawler = $this->client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('/users', $this->client->getRequest()->getPathInfo());
        $this->assertStringContainsString(
            "Superbe ! L'utilisateur a bien été ajouté.",
            $crawler->filter('.alert.alert-success')->text()
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
