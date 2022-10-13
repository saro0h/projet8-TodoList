<?php
// src/DataFixtures/Providers/EncodePasswordProvider.php
namespace App\DataFixtures\Providers;
 
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Provider\Base as BaseProvider;
 
class EncodePasswordProvider extends BaseProvider
{
    private $encoder;
 
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }
 
    public function encodePassword(string $plainPassword): string
    {
        return $this->encoder->hashPassword(new User(), $plainPassword);
    }
}