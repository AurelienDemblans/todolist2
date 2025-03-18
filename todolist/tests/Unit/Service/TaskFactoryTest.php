<?php

namespace App\Tests\Unit\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Service\TaskFactory;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class TaskFactoryTest extends TestCase
{
    private TaskFactory|null $taskFactory;
    private MockObject|null $securityMock;
    private User|null $user;

    public function setUp(): void
    {
        /** @var Security $securityMock */
        $this->securityMock = $this->getMockBuilder(Security::class)->disableOriginalConstructor()->getMock();

        $this->taskFactory = new TaskFactory($this->securityMock);
        $user = new User();
        $user->setUsername('testUser');
        $user->setEmail('testUser@test.com');
        $this->user = $user;
    }

    public function testSetCreatedByNotAllowed()
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage("On ne peut pas éditer le créateur d'une tâche.");

        $task = new Task();
        $task->setId(3);

        $this->taskFactory->setCreatedByOnTask($task);
    }

    public function testSetCreatedByNotAuthenticated()
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Aucun utilisateur connecté');

        $task = new Task();

        $this->taskFactory->setCreatedByOnTask($task);
    }

    public function testSetCreatedBy()
    {
        $task = new Task();

        $this->securityMock->method('getUser')->willReturn($this->user);

        $completedTask = $this->taskFactory->setCreatedByOnTask($task);

        self::assertInstanceOf(Task::class, $completedTask);
        self::assertInstanceOf(User::class, $completedTask->getCreatedBy());
        self::assertSame('testUser@test.com', $completedTask->getCreatedBy()->getEmail());
        self::assertSame('testUser', $completedTask->getCreatedBy()->getUsername());
    }

    public function tearDown(): void
    {
        $this->taskFactory = null;
        $this->securityMock = null;
        $this->user = null;
    }
}
