<?php

namespace App\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractKernelTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->kernelInterface = self::bootKernel();
        $this->validator = static::getContainer()->get('validator');
    }
}