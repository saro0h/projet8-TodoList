<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ManageUsers
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ObjectRepository */
    private $er;

    /** * @var UserPasswordEncoderInterface */
    private $encoder;

    /**
     * ManageUsers constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->er = $em->getRepository(User::class);
        $this->encoder = $encoder;
    }

    public function findUser($id)
    {
        return $this->er->findOneBy(['id' => $id]);
    }

    public function getAnonymousUser(){
        return $this->er->findOneBy(['username' => 'anonymous']);
    }

    public function createAnonymousUser(){
        $anonymous = new User();
        $anonymous->setUsername('anonymous');
        $anonymous->setPassword($this->encoder->encodePassword($anonymous, 'anonymous_password'));
        $anonymous->setEmail('no-reply@todolist.fr');

        $this->em->persist($anonymous);
        $this->em->flush();
    }

    /**
     * Check if specified user has admin role
     * @param User $user
     * @return bool
     */
    public function isAdmin(User $user){
        return false !== array_search(User::ROLE_ADMIN, $user->getRoles());
    }

    /**
     * Check if specified user is the anonymous account
     * @param User $user
     * @return bool
     */
    public function isAnonymous(User $user){
        return $this->getAnonymousUser() === $user;
    }
}