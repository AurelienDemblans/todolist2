<?php

namespace App\Service;

use App\Entity\Task;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
            throw new BadRequestHttpException("On ne peut pas éditer le créateur d'une tâche.");
        }

        if ($this->security->getUser() === null) {
            throw new BadRequestHttpException('Aucun utilisateur connecté');
        }

        $task->setCreatedBy($this->security->getUser());

        return $task;
    }
}
