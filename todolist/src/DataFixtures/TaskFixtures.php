<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
		$faker = Factory::create('fr_FR');

		foreach (UserFixtures::USER_FIXTURE_ARRAY as ['email' => $email]) {
			for ($i = 0; $i < 5; ++$i) {
				$task               = new Task();
				$date               = $faker->dateTime();
				$createdByReference = $email;
				$words              = $faker->words(3, true);
				/** @var string $words */
				$title = $createdByReference === 'anonyme@test.com' ? $words . ' (anonyme)' : $words;

				$task->setCreatedAt(\DateTimeImmutable::createFromMutable($date))
				->setContent($faker->text(100))
				->setTitle($title)
				->setIsDone(true)
				->setCreatedBy($this->getReference($createdByReference, User::class));

				$manager->persist($task);
			}
			for ($i = 0; $i < 5; ++$i) {
				$task               = new Task();
				$date               = $faker->dateTime();
				$createdByReference = $email;
				$words              = $faker->words(3, true);
				/** @var string $words */
				$title = $createdByReference === 'anonyme@test.com' ? $words . ' (anonyme)' : $words;

				$task->setCreatedAt(\DateTimeImmutable::createFromMutable($date))
				->setContent($faker->text(100))
				->setTitle($title)
				->setIsDone(false)
				->setCreatedBy($this->getReference($createdByReference, User::class));

				$manager->persist($task);
			}
		}

		$manager->flush();
	}

	/**
	 * @return string[]
	 */
	public function getDependencies(): array
	{
		return [UserFixtures::class];
	}
}
