<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function toListTasksTodo()
    {
        return $this->redirectToRoute('task_list_todo');
    }

    #[Route('/todo-list', name: 'task_list_todo')]
    public function listTaskIsDone(TaskRepository $taskRepository)
    {
        return $this->render('task/listTodo.html.twig', ['tasks' => $taskRepository->findByIsDone(0)]);
    }

    #[Route('/done-tasks', name: 'task_list_is_done')]
    public function listTaskTodo(TaskRepository $taskRepository)
    {
        return $this->render('task/listIsDone.html.twig', ['tasks' => $taskRepository->findByIsDone(1)]);
    }


    #[Route('/task/create', name: 'task_create')]
    public function createTask(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setAuthor($user);
            $em->persist($task);
            $em->flush();
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list_todo');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/task/{id}/edit', name: 'task_edit')]
    public function editTask(Task $task, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            if ($task->isDone()) return $this->redirectToRoute('task_list_is_done');
            return $this->redirectToRoute('task_list_todo');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/task/{id}/toggle', name: 'task_toggle')]
    public function toggleTask(Task $task, EntityManagerInterface $em)
    {
        $task->toggle(!$task->isDone());
        $em->flush();
        if ($task->isDone()) {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
            return $this->redirectToRoute('task_list_todo');
        } else {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme à faire.', $task->getTitle()));
            return $this->redirectToRoute('task_list_is_done');
        }
    }

    #[Route('/task/{id}/delete', name: 'task_delete')]
    public function deleteTask(Task $task, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        if ($task->getAuthor() !== $user && ($task->getAuthor()->getUsername() == "anonyme" && !$this->isGranted('ROLE_ADMIN'))) {
            $this->addFlash('danger', 'Cette tâche ne peut être supprimé que par son auteur.');
        } else {
            if ($task->getAuthor()->getUsername() == "anonyme" && $this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('success', 'Cette tâche a été supprimé par un administrateur.');
            } else {
                $this->addFlash('success', 'La tâche a bien été supprimée.');
            }
            $em->remove($task);
            $em->flush();
        }

        if ($task->isDone()) return $this->redirectToRoute('task_list_is_done');
        return $this->redirectToRoute('task_list_todo');
    }
}
