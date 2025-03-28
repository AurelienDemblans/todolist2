<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\RoleProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
	public function __construct(private readonly UserPasswordHasherInterface $hasher)
	{
	}

	public const USER_FIXTURE_ARRAY = [
		['username' => 'John', 'roles' => RoleProvider::ROLE_ADMIN, 'password' => '12345', 'email' => 'john@test.com'],
		['username' => 'Henri', 'roles' => RoleProvider::ROLE_ADMIN, 'password' => '12345', 'email' => 'henri@test.com'],
		['username' => 'Marc', 'roles' => RoleProvider::ROLE_USER, 'password' => '12345', 'email' => 'marc@test.com'],
		['username' => 'Louis', 'roles' => RoleProvider::ROLE_USER, 'password' => '12345', 'email' => 'louis@test.com'],
		['username' => 'Anonyme', 'roles' => RoleProvider::ROLE_ADMIN, 'password' => '12345', 'email' => 'anonyme@test.com'],
	];

	public function load(ObjectManager $manager): void
	{
		foreach (self::USER_FIXTURE_ARRAY as ['username' => $username,'roles' => $role, 'password' => $password, 'email' => $email]) {
			$user = new User();

			if (!RoleProvider::isValidRole($role)) {
				throw new Exception("Le rôle n'existe pas");
			}

			$user->setUsername($username)
			->setRoles([$role])
			->setPassword($this->hasher->hashPassword($user, $password))
			->setEmail($email);

			$manager->persist($user);

			$this->addReference($email, $user);
		}

		$manager->flush();
	}
}
