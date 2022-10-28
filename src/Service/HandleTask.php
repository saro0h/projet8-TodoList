<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class HandleTask
{
    public function __construct(
        private EntityManagerInterface $manager
    ) {
    }

    public function createTask($task): void
    {
        $this->manager->persist($task);
        $this->manager->flush();

        $session = new Session();
        $session->getFlashbag()->add(
            'success',
            'La tâche a été bien été ajoutée.'
        );
    }

    public function editTask(): void
    {
        $this->manager->flush();

        $session = new Session();
        $session->getFlashbag()->add(
            'success',
            'La tâche a bien été modifiée.'
        );
    }

    public function toggleTask($task): void
    {
        $task->toggle(!$task->isDone());
        $this->manager->flush();

        $session = new Session();
        $session->getFlashbag()->add(
            'success',
            sprintf(
                'La tâche %s a bien été marquée comme faite.',
                $task->getTitle()
            )
        );
    }

    public function deleteTask($task): void
    {
        $this->manager->remove($task);
        $this->manager->flush();

        $session = new Session();
        $session->getFlashbag()->add(
            'success',
            'La tâche a bien été supprimée.'
        );
    }
}
