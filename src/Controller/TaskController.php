<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    public function __construct(private readonly TaskRepository $taskRepository)
    {
    }

    #[Route(path: '/tasks', name: 'task_list', methods: 'GET')]
    public function list(): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $this->taskRepository->findBy([
                'isDone' => false,
            ])
        ]);
    }

    #[Route(path: '/tasks/finished', name: 'task_finished', methods: 'GET')]
    public function finished(): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $this->taskRepository->findBy([
                'isDone' => true,
            ])
        ]);
    }

    #[Route(path: '/tasks/create', name: 'task_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $task->setUser($user);
            $this->taskRepository->save($task, true);

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/tasks/{id}/edit', name: 'task_edit', methods: ['GET', 'POST'])]
    public function edit(Task $task, Request $request): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $this->taskRepository->save($task, true);

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route(path: '/tasks/{id}/toggle', name: 'task_toggle', methods: 'GET')]
    public function toggleTask(Task $task): Response
    {
        $task->toggle(!$task->isDone());
        $this->taskRepository->save($task, true);

        if ($task->isDone()) {
            $this->addFlash('error', sprintf('La tâche %s a bien été marquée comme terminée.', $task->getTitle()));

            return $this->redirectToRoute('task_finished');
        }
        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme à réaliser.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * @IsGranted("TASK_DELETE", subject="task")
     */
    #[Route(path: '/tasks/{id}/delete', name: 'task_delete', methods: 'GET')]
    public function deleteTask(Task $task): Response
    {
        $this->taskRepository->remove($task, true);

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
