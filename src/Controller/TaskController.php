<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    #[Route('/', name: 'task_list')]
    public function listTaskIsDone(TaskRepository $taskRepository)
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findByIsDone(0)]);
    }

    #[Route('/tasks', name: 'homepage')]
    public function listTaskTodo(TaskRepository $taskRepository)
    {
        return $this->render('default/index.html.twig', ['tasks' => $taskRepository->findByIsDone(1)]);
    }


    #[Route('/taks/create', name: 'task_create')]
    public function createTask(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setAuthor($user);
            dd($task);
            $em->persist($task);
            $em->flush();
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/taks/{id}/edit', name: 'task_edit')]
    public function editTask(Task $task, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTask(Task $task, EntityManagerInterface $em)
    {
        $task->toggle(!$task->isDone());
        $em->flush();
        if ($task->isDone()) {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
            return $this->redirectToRoute('task_list');
        } else {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme à faire.', $task->getTitle()));
            return $this->redirectToRoute('homepage');
        }
    }

    #[Route('/taks/{id}/delete', name: 'task_delete')]
    public function deleteTask(Task $task, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        if ($task->getAuthor() !== $user) {
            $this->addFlash('error', 'Cette tâche ne peut être supprimé que par son auteur.');
        } else {
            $em->remove($task);
            $em->flush();
            $this->addFlash('success', 'La tâche a bien été supprimée.');
        }

        return $this->redirectToRoute('task_list');
    }
}
