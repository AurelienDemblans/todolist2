<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $j = 0;
        for ($i = 0; $i < 15; $i++) {
            if ($j > (count(UserFixtures::USER_FIXTURE_ARRAY) - 1)) {
                $j = 0;
            }
            $task = new Task();
            $date = $faker->dateTime();
            $createdByReference = UserFixtures::USER_FIXTURE_ARRAY[$j]['email'];
            $title = $createdByReference === 'anonyme@test.com' ? $faker->words(3, true). ' (anonyme)' : $faker->words(3, true);

            $task->setCreatedAt(\DateTimeImmutable::createFromMutable($date))
            ->setContent($faker->text(100))
            ->setTitle($title)
            ->setIsDone($faker->boolean())
            ->setCreatedBy($this->getReference($createdByReference, User::class));

            $manager->persist($task);
            $j++;
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
