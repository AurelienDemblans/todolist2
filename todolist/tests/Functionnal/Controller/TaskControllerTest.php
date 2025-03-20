<?php

namespace App\Tests\Functionnal\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private UserRepository|null $userRepository = null;
    private TaskRepository|null $taskRepository = null;
    private object|null $urlGenerator = null;
    private array $userEmails = ['admin' => "john@test.com", 'user' => "marc@test.com"];
    private string $listRouteName = "task_list";
    private string $editRouteName = 'task_edit';
    private string $createRouteName = 'task_create';

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();

        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
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

    public function testListIsDonePageAsRoleUser()
    {
        $this->logAsRoleUser();

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->listRouteName, ['isDone' => true]));
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.container h3', 'Liste des tâches terminées');

        $this->assertCount(
            10,
            $crawler->filter('div.card')
        );

        $this->assertCount(
            2,
            $crawler->filter('h5 a[href^="/tasks/"][href$="/edit"]')
        );
        $this->assertCount(
            2,
            $crawler->selectButton('Marquer non terminée')
        );
    }

    public function testListWrongQueryParams()
    {
        $this->logAsRoleUser();

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->listRouteName, ['isDone' => 'test']));
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString("Le parametre de requête dans l'url n'est pas correct.", $this->client->getResponse()->getContent());
    }

    public function testListNotDonePageAsRoleUser()
    {
        $this->logAsRoleUser();

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->listRouteName, ['isDone' => false]));
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.container h3', 'Liste des tâches à faire');

        $this->assertCount(
            7,
            $crawler->filter('div.card')
        );

        $this->assertCount(
            1,
            $crawler->filter('h5 a[href^="/tasks/"][href$="/edit"]')
        );
    }

    public function testListNotDonePageAsRoleAdmin()
    {
        $this->logAsRoleAdmin();

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->listRouteName, ['isDone' => false]));
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.container h3', 'Liste des tâches à faire');
        $cardsNumber = $crawler->selectButton('Marquer comme faite')->count();

        $this->assertCount(
            $cardsNumber,
            $crawler->filter('div.card')
        );

        $this->assertCount(
            $cardsNumber,
            $crawler->filter('h5 a[href^="/tasks/"][href$="/edit"]')
        );
    }

    public function testEditPageAsRoleAdmin()
    {
        $this->logAsRoleAdmin();

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->editRouteName, ['id' => 1]));
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertCount(
            1,
            $crawler->filter('#task_title')
        );
        $this->assertCount(
            1,
            $crawler->filter('#task_content')
        );
        $this->assertCount(
            2,
            $crawler->filter('input[type=radio]')
        );
        $form = $crawler->selectButton('Modifier')->form();
        $this->assertSame('task', $form->getName());
    }

    public function testSubmitEditAsRoleAdmin()
    {
        $this->logAsRoleAdmin();

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->editRouteName, ['id' => 1]));
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Modifier')->form();

        $form['task[title]'] = 'test title';
        $crawler = $this->client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame($this->urlGenerator->generate($this->listRouteName), $this->client->getRequest()->getPathInfo());
        $this->assertStringContainsString(
            "Superbe ! La tâche a bien été modifiée.",
            $crawler->filter('.alert.alert-success')->text()
        );
    }

    public function testEditTaskCreatedByOtherUser()
    {
        $this->logAsRoleUser();

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->editRouteName, ['id' => 2]));
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertStringContainsString("Vous ne pouvez pas modifier les tâches d'autres utilisateurs.", $this->client->getResponse()->getContent());
    }

    public function testToggle()
    {
        $this->logAsRoleAdmin();

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->listRouteName));
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $cardsNumber = $crawler->selectButton('Marquer comme faite')->count();
        $firstCard = $crawler->selectButton('Marquer comme faite')->first();
        $form = $firstCard->form();

        $crawler = $this->client->submit($form);
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $updatedCardsNumber = $crawler->selectButton('Marquer comme faite')->count();
        $this->assertSame($this->urlGenerator->generate($this->listRouteName), $this->client->getRequest()->getPathInfo());

        $this->assertSame($updatedCardsNumber, ($cardsNumber - 1));
        $this->assertCount(
            1,
            $crawler->filter('.alert.alert-success')
        );
        self::assertEquals(true, $this->taskRepository->find(1)->isDone());
    }

    public function testCreatePageAsRoleAdmin()
    {
        $this->logAsRoleAdmin();

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->createRouteName));
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertCount(
            1,
            $crawler->filter('#task_title')
        );
        $this->assertCount(
            1,
            $crawler->filter('#task_content')
        );
        $this->assertCount(
            2,
            $crawler->filter('input[type=radio]')
        );
        $form = $crawler->selectButton('Ajouter')->form();
        $this->assertSame('task', $form->getName());
    }

    public function testSubmitCreateAsRoleAdmin()
    {
        $this->logAsRoleAdmin();

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($this->createRouteName));
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $form = $crawler->selectButton('Ajouter')->form();

        $form['task[title]'] = 'test create task';
        $form['task[content]'] = 'test content';
        $form['task[is_done]'] = 0;

        $crawler = $this->client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame($this->urlGenerator->generate($this->listRouteName), $this->client->getRequest()->getPathInfo());
        $this->assertStringContainsString(
            "Superbe ! La tâche a été bien été ajoutée.",
            $crawler->filter('.alert.alert-success')->text()
        );
        self::assertSame(1, count($this->taskRepository->findByTitle('test create task')));
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
