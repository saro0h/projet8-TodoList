<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Service\HandleTask;

class TaskController extends AbstractController
{
    public function __construct(
        private TaskRepository $taskRepository
    ) {
    }

    #[Route('/tasks', name: 'task_list')]
    public function listAction(): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $this->taskRepository->findAll(),
        ]);
    }

    #[Route('/tasks/create', name: 'task_create')]
    public function createAction(
        Request $request,
        HandleTask $handleTask
    ): Response {
        $user = $this->getUser();
        $task = new Task();
        $task->setUser($user);

        $form = $this->createForm(
            TaskType::class,
            $task
        )->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $handleTask->createTask($task);
            return $this->redirectToRoute('task_list');
        }

        return $this->renderForm(
            'task/create.html.twig',
            ['form' => $form]
        );
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function editAction(
        Task $task,
        Request $request,
        HandleTask $handleTask
    ): Response {
        // check for "authorize" access: calls all voters
        $this->denyAccessUnlessGranted('authorize', $task);

        $form = $this->createForm(
            TaskType::class,
            $task
        )->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $handleTask->editTask();
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTaskAction(
        Task $task,
        HandleTask $handleTask
    ): RedirectResponse {
        // check for "authorize" access: calls all voters
        $this->denyAccessUnlessGranted('authorize', $task);

        $handleTask->toggleTask($task);
        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTaskAction(
        Task $task,
        HandleTask $handleTask
    ): RedirectResponse {
        $this->denyAccessUnlessGranted('authorize', $task);

        $handleTask->deleteTask($task);
        return $this->redirectToRoute('task_list');
    }
}
