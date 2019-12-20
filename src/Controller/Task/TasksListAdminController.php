<?php

namespace App\Controller\Task;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class TasksListAdminController extends AbstractController
{
    /**
     * @Route("/tasks/admin", name="task_list_admin")
     */
    public function listAdmin()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $tasks = $this->getDoctrine()->getRepository(Task::class)->findAll();
  
        return $this->render('task/taskslist.html.twig', ['tasks' => $tasks]);
    }
}