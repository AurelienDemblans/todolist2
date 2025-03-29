<?php

namespace App\Service;

use App\Entity\User;
use LogicException;
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
        if ($user->getPassword() === null) {
            throw new LogicException("Une erreur imprévu est survenue, le mot de passe n'a pas été récupéré.");
        }
        $password = $this->passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );
        $user->setPassword($password);

        if (is_string($form->get('role')->getData()) && !RoleProvider::isValidRole($form->get('role')->getData())) {
            throw new BadRequestHttpException('Le role attribué est invalide.');
        }

        $role = $form->get('role')->getData();
        /** @var string $role */
        $user->setRoles([$role]);

        return $user;
    }
}
