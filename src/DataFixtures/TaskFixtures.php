<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Task;
use App\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $task = new Task();
            $task->setTitle("Tâche N° $i")
                ->setContent("Lorem Ipsum is simply dummy text of the printing
                 and typesetting industry. Lorem Ipsum has been the industry's
                 standard dummy text ever since the 1500s, when an unknown printer
                 took a galley of type and scrambled it to make a type specimen book.
                 It has survived not only five centuries, but also the leap into electronic
                 typesetting, remaining essentially unchanged. It was popularised in the 1960s
                 with the release of Letraset sheets containing Lorem Ipsum passages, and more
                 recently with desktop publishing software like Aldus PageMaker including
                 versions of Lorem Ipsum.")
                ->setCreatedAt(new \DateTime('2022-01-01T10:00:00+00:00'));

            $manager->persist($task);
        }

        // création de 3 tâches (qui ne soient pas anonyme)
        // reliées à des users (avec différents roles) pour les tests
        $task = new Task();
        $task->setTitle("Tâche test créée par admin")
            ->setContent("Pour test les EDIT, TOGGLE et DELETE par d'autres users.")
            ->setCreatedAt(new \DateTime('2022-01-01T10:00:00+00:00'))
            ->setUser($this->getReference(UserFixtures::USER_ADMIN));
        $manager->persist($task);

        $task = new Task();
        $task->setTitle("Tâche test créée par un user")
            ->setContent("Pour test les EDIT, TOGGLE et DELETE par d'autres users.")
            ->setCreatedAt(new \DateTime('2022-01-01T10:00:00+00:00'))
            ->setUser($this->getReference(UserFixtures::USER_TEST_1));
        $manager->persist($task);

        $task = new Task();
        $task->setTitle("Tâche test créée par un autre user")
            ->setContent("Pour test les EDIT, TOGGLE et DELETE par d'autres users.")
            ->setCreatedAt(new \DateTime('2022-01-01T10:00:00+00:00'))
            ->setUser($this->getReference(UserFixtures::USER_TEST_2));

        $manager->persist($task);
        $manager->flush();
    }

    // return an array of the fixture classes that must be loaded before this one, here UserFixtures
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
