<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;

/**
 * TaskRepository
 */
class TaskRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllOrderedByName()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT t FROM AppBundle:Task t ORDER BY t.title ASC'
            )
            ->getResult();
    }

    public function findByUser(User $user)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT t FROM AppBundle:Task t WHERE t.user = :user '
            )
            ->setParameter('user', $user)
            ->getResult();
    }
}