<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TaskFactory
{
	public function __construct(private readonly Security $security)
	{
	}

	/**
	 * @param Task $task
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

		$user = $this->security->getUser();
		if (!$user instanceof User) {
			throw new BadRequestHttpException('L\'utilisateur n\'est pas du bon type');
		}

		$task->setCreatedBy($user);

		return $task;
	}
}
