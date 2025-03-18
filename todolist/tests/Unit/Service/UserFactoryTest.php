<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Service\UserFactory;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactoryTest extends TestCase
{
    private UserFactory|null $userFactory;
    private MockObject|null $passwordHasher;
    private MockObject|null $form;
    private MockObject|null $formInterface;

    public function setUp(): void
    {
        /** @var UserPasswordHasherInterface&MockObject $passwordHasher */
        $this->passwordHasher = $this->getMockBuilder(UserPasswordHasherInterface::class)->getMock();
        $this->passwordHasher->method('hashPassword')->willReturn('testPassword');

        $this->formInterface = $this->getMockBuilder(FormInterface::class)->disableOriginalConstructor()->getMock();

        /** @var Form&MockObject $form */
        $this->form = $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
        $this->form->method('get')->willReturn($this->formInterface);

        $this->userFactory = new UserFactory($this->passwordHasher);
    }

    public function testCompleteUserInvalidRole()
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Le role attribué est invalide.');

        $user = new User();
        $user->setPassword(12345);

        $this->formInterface->method('getData')->willReturn('INVALID_ROLE');

        $this->userFactory->completeUser($user, $this->form);
    }

    //* si on moque le password hasher au final le test ne vérifie pas grand chose à part le settages des valeurs. Et en meme temps je dois faire confiance aux services que j'utilise donc je ne vais pas les tester non plus ?
    public function testCompleteUserValidData()
    {
        $user = new User();
        $user->setPassword('testPassword');

        $this->formInterface->method('getData')->willReturn('ROLE_ADMIN');

        $completedUser = $this->userFactory->completeUser($user, $this->form);

        self::assertInstanceOf(User::class, $completedUser);
        self::assertIsArray($completedUser->getRoles());
        self::assertContains('ROLE_ADMIN', $completedUser->getRoles());
        self::assertSame('testPassword', $completedUser->getPassword());
    }

    public function tearDown(): void
    {
        $this->userFactory = null;
        $this->passwordHasher = null;
        $this->form = null;
    }
}
