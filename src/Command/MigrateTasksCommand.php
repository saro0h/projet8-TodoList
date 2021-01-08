<?php


namespace App\Command;


use App\Entity\Task;
use App\Service\ManageUsers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MigrateTasksCommand extends Command
{
    protected static $defaultName = 'app:migrate-tasks';

    /** @var EntityManagerInterface */
    private $em;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /** @var ManageUsers */
    private $manageUsers;

    /**
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @param ManageUsers $manageUsers
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, ManageUsers $manageUsers)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->manageUsers = $manageUsers;

        parent::__construct();
    }


    protected function configure()
    {
        $this->setDescription('Attach old tasks to anonymous user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $anonymous = $this->manageUsers->getAnonymousUser();

        if (!$anonymous) {
            $output->writeln('No anonymous user found, creating user...');
            $this->manageUsers->createAnonymousUser();
        }

        $taskRep = $this->em->getRepository(Task::class);
        $tasks = $taskRep->findAll();

        foreach ($tasks as $task){
            if (null === $this->manageUsers->findUser($task->getUser()->getId())) {
                $output->writeln('Task with id #'.$task->getId().' attached to anonymous user');
                $anonymous->addTask($task);
                $this->em->persist($anonymous);
            }
        }

        $this->em->flush();
        return Command::SUCCESS;
    }
}