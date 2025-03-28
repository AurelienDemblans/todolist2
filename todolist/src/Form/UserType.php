<?php

namespace App\Form;

use App\Entity\User;
use App\Service\RoleProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$user        = $options['data'] ?? null;
		$userHasId   = $user instanceof User && $user->getId() !== null;
		$defaultRole = $userHasId ? $user->getRoles()[0] : RoleProvider::ROLE_USER;

		$builder
			->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
			->add('password', RepeatedType::class, [
				'type'            => PasswordType::class,
				'invalid_message' => 'Les deux mots de passe doivent correspondre.',
				'required'        => true,
				'first_options'   => ['label' => 'Mot de passe'],
				'second_options'  => ['label' => 'Tapez le mot de passe à nouveau'],
			])
			->add('email', EmailType::class, ['label' => 'Adresse email'])
			->add('role', ChoiceType::class, [
				'label'    => 'Rôle',
				'choices'  => RoleProvider::getRoleList(),
				'expanded' => false,
				'multiple' => false,
				'required' => true,
				'mapped'   => false,
				'data'     => $defaultRole,
			])
		;
	}
}
