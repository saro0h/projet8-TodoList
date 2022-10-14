<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function toListTasksCounter(TaskRepository $taskRepository)
    {
        return $this->render('homepage/homepage.html.twig', ['tasks' => $taskRepository->findAll()]);
    }

    #[Route('/tasks/todo', name: 'task_list_todo')]
    public function listTaskIsDone(TaskRepository $taskRepository)
    {
        $tasksTodo = $taskRepository->findBy(['isDone' => 0], ['createdAt' => 'DESC']);
        return $this->render('task/listTodo.html.twig', ['tasks' => $tasksTodo]);
    }

    #[Route('/tasks/done', name: 'task_list_done')]
    public function listTaskTodo(TaskRepository $taskRepository)
    {
        return $this->render('task/listIsDone.html.twig', ['tasks' => $taskRepository->findBy(['isDone' => 1], ['createdAt' => 'DESC'])]);
    }


    #[Route('/task/create', name: 'task_create')]
    public function createTask(Request $request, EntityManagerInterface $emi)
    {
        $user = $this->getUser();
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setAuthor($user);
            $task->setCreatedAt(new \DateTimeImmutable('NOW'));
            $emi->persist($task);
            $emi->flush();
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list_todo');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/task/{id}/edit', name: 'task_edit')]
    public function editTask(Task $task, Request $request, EntityManagerInterface $emi)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $emi->flush();
            $this->addFlash('success', 'La tâche a bien été modifiée.');
            if ($task->getIsDone()) return $this->redirectToRoute('task_list_done');
            return $this->redirectToRoute('task_list_todo');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/task/{id}/toggle', name: 'task_toggle')]
    public function toggleTask(Task $task, EntityManagerInterface $emi)
    {
        $task->setIsDone(!$task->getIsDone());
        $emi->flush();
        if ($task->getIsDone()) {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
            return $this->redirectToRoute('task_list_todo');
        } else {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme à faire.', $task->getTitle()));
            return $this->redirectToRoute('task_list_done');
        }
    }

    #[Route('/task/{id}/delete', name: 'task_delete')]
    public function deleteTask(Task $task, EntityManagerInterface $emi)
    {
        $this->denyAccessUnlessGranted('delete_task', $task);
        $emi->remove($task);
        $emi->flush();
        $this->addFlash('success', 'La tâche a bien été supprimée.');

        if ($task->getIsDone()) return $this->redirectToRoute('task_list_done');
        return $this->redirectToRoute('task_list_todo');
    }
}
