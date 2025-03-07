<?php

namespace App\DataFixtures;

use App\AppBundle\Entity\Task;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $task = new Task();

        $task->setContent('blablalba');
        $task->setCreatedAt(new DateTimeImmutable());
        $task->setTitle('blablalba');
        $task->setIsDone(false);

        $manager->persist($task);

        $task2 = new Task();

        $task2->setContent('btask2 TASTlablalba');
        $task2->setCreatedAt(new DateTimeImmutable());
        $task2->setTitle('DEUXIEME');
        $task2->setIsDone(true);

        $manager->persist($task2);

        $manager->flush();
    }
}
