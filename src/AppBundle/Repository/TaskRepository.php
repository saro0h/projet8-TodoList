<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class TaskRepository extends EntityRepository
{
    public function findAllOrderedByDate()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM AppBundle:Task p ORDER BY p.created_at ASC'
            )
            ->getResult();
    }

    public function findTasksByUser(User $user)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT t FROM AppBundle:Task t '.
                'WHERE t.user > :user'
        )->setParameter('user', $user->getUsername());

        return $query->getResult();
    }

    public function findTasksByUserId($userId)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT t FROM AppBundle:Task t '.
                'WHERE t.user = :user'
        )->setParameter('user', $userId);

        return $query->getResult();
    }
}