<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Service\RoleProvider;
use App\Service\TaskFactory;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly TaskFactory $taskFactory)
    {
    }

    #[Route('/tasks', name: 'task_list', methods: Request::METHOD_GET) ]
    public function listAction(Request $request, TaskRepository $taskRepository)
    {
        $isDone = filter_var($request->get('isDone', false), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($isDone === null) {
            throw new Exception("Le parametre de requête dans l'url n'est pas correct.");
        }

        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findByIsDone($isDone), 'title' => $isDone ? 'Liste des tâches terminées' : 'Liste des tâches à faire']);
    }

    #[Route('/tasks/create', name: 'task_create', methods: [Request::METHOD_POST, Request::METHOD_GET])]
    #[IsGranted('ROLE_USER')]
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task, [
            'from' => 'ADD'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $this->taskFactory->setCreatedByOnTask($task);

            $this->em->persist($task);
            $this->em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list', ['isDone' => $task->isDone()]);
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit', methods: [Request::METHOD_POST, Request::METHOD_GET])]
    #[IsGranted('ROLE_USER')]
    public function editAction(Task $task, Request $request)
    {
        if ($task->getCreatedBy() !== $this->getUser() && !$this->isGranted(RoleProvider::ROLE_ADMIN)) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas modifier les tâches d'autres utilisateurs.");
        }
        $form = $this->createForm(TaskType::class, $task, [
            'from' => 'EDIT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list', ['isDone' => $task->isDone()]);
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle', methods: [Request::METHOD_PATCH, Request::METHOD_GET]) ]
    public function toggleTaskAction(Task $task)
    {
        $task->toggle(!$task->isDone());
        $this->em->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete', methods: [Request::METHOD_DELETE, Request::METHOD_GET])]
    #[IsGranted('ROLE_USER')]
    public function deleteTaskAction(Task $task)
    {
        if ($task->getCreatedBy()->getEmail() === 'anonyme@test.com' && !$this->isGranted(RoleProvider::ROLE_ADMIN)) {
            throw new Exception("Les tâches liées à l'utilisateur anonyme peuvent uniquement être supprimées par des administrateur.");
        } elseif ($task->getCreatedBy() !== $this->getUser()) {
            throw new Exception("Vous ne pouvez pas supprimer les tâches créer par d'autres utilisateurs.");
        }

        $this->em->remove($task);
        $this->em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
