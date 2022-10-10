<?php

namespace App\Tests\Unit\Repository;

use Faker\Factory;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRespositoryTest extends WebTestCase
{

    public function testSaveUser()
    {
        $faker = Factory::create('fr-FR');
        $userRepository = static::getContainer()->get(UserRepository::class);
        $counterBefore = $userRepository->count([]);
        $user =  (new User())->setUsername($faker->name())->setEmail($faker->email())->setPassword('password');
        $userRepository->save($user, true);
        $counterAfter = $userRepository->count([]);
        $this->assertEquals($counterBefore + 1, $counterAfter);
    }

    public function testRemoveUser()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $counterBefore = $userRepository->count([]);
        $user = $userRepository->findOneBy([], ['id' => 'DESC']);
        $userRepository->remove($user, true);
        $counterAfter = $userRepository->count([]);
        $this->assertEquals($counterBefore - 1, $counterAfter);
    }
}
