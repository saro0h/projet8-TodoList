<?php

namespace App\Services;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RightToDeleteTaskService extends AbstractController
{
    public function control(Task $task): bool
    {

        $user = $this->getUser();

        $action = false;

        if ($task->getAuthor()->getUsername() == "anonyme" && $this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('success', 'Cette tâche a été supprimé par un administrateur.');
            $action = true;
        }
        if (!$action && $task->getAuthor() !== $user) {
            if ($task->getAuthor()->getUsername() == "anonyme") {
                $this->addFlash('danger', 'Cette tâche ne peut être supprimé que par un administrateur.');
            } else {
                $this->addFlash('danger', 'Cette tâche ne peut être supprimé que par son auteur.');
            }
        }
        if (!$action && $task->getAuthor() === $user) {
            $this->addFlash('success', 'La tâche a bien été supprimée.');
            $action = true;
        }
        return $action;
    }
}
