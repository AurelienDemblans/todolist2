<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * @param User $user
     * @param FormInterface $form
     *
     * @return User
     */
    public function completeUser(User $user, FormInterface $form): User
    {
        $password = $this->passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );
        $user->setPassword($password);

        if (!RoleProvider::isValidRole($form->get('role')->getData())) {
            throw new BadRequestHttpException('Le role attribué est invalide.');
        }

        $user->setRoles([$form->get('role')->getData()]);

        return $user;
    }
}
