<?php

namespace App\DataFixtures;

use App\AppBundle\Entity\Task;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 15; $i++) {
            $task = new Task();
            $date = $faker->dateTime();

            $task->setCreatedAt(\DateTimeImmutable::createFromMutable($date))
            ->setContent($faker->text(100))
            ->setTitle($faker->words(3, true))
            ->setIsDone($faker->boolean());

            $manager->persist($task);
        }

        $manager->flush();
    }
}
