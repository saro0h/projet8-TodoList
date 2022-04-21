<?php

namespace App\Tests;

use App\Entity\Task;
use DateTimeImmutable;
use Exception;
use Faker\Factory as Faker;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionUnionType;

class EntityGetSetTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetters(): void
    {
        $bookReflection = new ReflectionClass(Task::class);

        $task = (new Task);

        foreach ($bookReflection->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->getName() == 'id') {
                continue;
            }

            $propertyFakeValue = $this->getFakeValue($reflectionProperty->getType());
            $propertyName = $reflectionProperty->getName();

            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($task, $propertyFakeValue);

            $getterName = 'get' . ucfirst($propertyName);

            if (!$bookReflection->hasMethod($getterName)) {
                continue;
            }

            self::assertEquals($propertyFakeValue, $task->$getterName());
        }
    }

    public function testSetters(): void
    {
        $bookReflection = new ReflectionClass(Task::class);

        $task = (new Task);

        foreach ($bookReflection->getProperties() as $reflectionProperty) {
            $propertyFakeValue = $this->getFakeValue($reflectionProperty->getType());
            $propertyName = $reflectionProperty->getName();

            $setterName = 'set' . ucfirst($propertyName);

            if (!$bookReflection->hasMethod($setterName)) {
                continue;
            }

            $task->$setterName($propertyFakeValue);

            self::assertEquals($propertyFakeValue, $reflectionProperty->getValue($task));
        }
    }

    private function getFakeValue(ReflectionUnionType|ReflectionNamedType|null $reflectionType)
    {
        if ($reflectionType instanceof ReflectionUnionType) {
            $typeName = $reflectionType->getTypes()[0]->getName();
        } elseif ($reflectionType instanceof ReflectionNamedType) {
            $typeName = $reflectionType->getName();
        } else {
            return null;
        }

        $faker = Faker::create();

        return match ($typeName) {
            'int' => $faker->numberBetween(),
            'string' => $faker->text(),
            'DateTime' | 'DateTimeImmutable' => new DateTimeImmutable(),
            'bool' => true,
            default => throw new Exception
        };
    }
}
