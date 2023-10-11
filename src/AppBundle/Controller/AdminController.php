<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Form\TaskType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    /**
     * @Route("/admin/tasks", name="admin_tasks_list")
     */
    public function taskslistAction()
    {
        $tasks = $this->getDoctrine()->getRepository('AppBundle:Task')->findAll();

        //sdump($tasks);die();
        return $this->render('admin/tasks_list.html.twig',
        [
            'tasks' => $tasks
        ]);
    }

    /**
     * @Route("/admin/users", name="admin_users_list")
     */
    public function userslistAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();

        return $this->render('admin/users_list.html.twig',
        [
            'users' => $users
        ]);
    }
}
