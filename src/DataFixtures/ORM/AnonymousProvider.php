<?php

namespace App\DataFixtures\ORM;

use Faker\Provider\Base as BaseProvider;

final class AnonymousProvider extends BaseProvider
{
    public function anonymous(): string
    {
        return 'anonymous';
    }
}