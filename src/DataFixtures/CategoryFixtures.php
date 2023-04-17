<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category1 = new Category();
        $category1->setTitle('category one');
        $this->addReference('category1', $category1);

        $manager->persist($category1);

        $category2 = new Category();
        $category2->setTitle('category two');
        $this->addReference('category2', $category2);

        $manager->persist($category2);

        $manager->flush();
    }
}
