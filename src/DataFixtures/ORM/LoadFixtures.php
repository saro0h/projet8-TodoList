<?php

namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Nelmio\Alice\Loader\NativeLoader;

class LoadFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $loader = new NativeLoader();
        $objectSet = $loader->loadFile(__DIR__ . '/fixtures.yaml')->getObjects();
        foreach($objectSet as $object)
        {
            $manager->persist($object);
        }

        $manager->flush();
    }
}