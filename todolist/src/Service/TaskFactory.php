<?php

namespace App\Service;

use App\Entity\Task;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;

class TaskFactory
{
    public function __construct(private readonly Security $security)
    {
    }

    /**
     * @param  Task $task
     *
     * @return task
     */
    public function setCreatedByOnTask(Task $task): Task
    {
        if ($task->getId() !== null) {
            throw new Exception("On ne peut pas éditer le créateur d'une tâche.");
        }

        if ($this->security->getUser() === null) {
            throw new Exception('Aucun utilisateur connecté');
        }

        $task->setCreatedBy($this->security->getUser());

        return $task;
    }
}
