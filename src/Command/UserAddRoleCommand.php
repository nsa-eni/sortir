<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAddRoleCommand extends Command
{
    protected static $defaultName = 'user:create-admin';

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface $passwordEncoderstring
     */
    private $passwordEncoderstring;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoderstring)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoderstring = $passwordEncoderstring;
        parent::__construct(self::$defaultName);
    }

    protected function configure()
    {
        $this
            ->setDescription('Create a user admin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        $question1 = new Question("Enter email: ", "guest");
        $email = $helper->ask($input, $output, $question1);

        $question2 = new Question("Enter password: ", "guest");
        $password = $helper->ask($input, $output, $question2);

        if ($this->createUser($email, $password)){
            $message1 = sprintf("Email: %s", $email);
            $message2 = sprintf("Password: %s", $password);
            $output->writeln($message1);
            $output->writeln($message2);
            $io->success('User admin has been created');
        }else
            $io->error("Something wrong!");

    }

    /**
     * @param string $email
     * @param string $password
     * @return bool
     */
    private function createUser(string $email, string $password)
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPseudo("admin-$email");
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoderstring->encodePassword($user, $password));
        $user->setAdministrator(true);
        try{
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return true;
        }catch (ORMException $exception)
        {
            print $exception->getMessage();
        }
        return false;
    }
}
