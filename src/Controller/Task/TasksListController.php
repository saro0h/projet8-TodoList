<?php

namespace App\Controller\Task;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TasksListController extends AbstractController
{
    /**
     * @Route("/tasks/", name="task_list")
     */
    public function listAction()
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $tasks = $this->getDoctrine()->getRepository(Task::class)->findBy(['user' => $user]);
        
        return $this->render('task/list.html.twig', ['tasks' => $tasks, 'user' => $user]);
    }
}
