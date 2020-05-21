<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $task1 = new Task();
        $task1->setTitle('task one');
        $task1->setContent('my great content');
        $task1->setCreatedAt(new \DateTime());
        $task1->setUser($this->getReference('admin'));

        $manager->persist($task1);

        $task2 = new Task();
        $task2->setTitle('task two');
        $task2->setContent('my other content');
        $task2->setCreatedAt(new \DateTime());
        $task2->setUser($this->getReference('user'));

        $manager->persist($task2);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}