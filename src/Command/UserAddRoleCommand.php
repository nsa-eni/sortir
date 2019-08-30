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
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAddRoleCommand extends Command
{
    protected static $defaultName = 'user:create-admin';
    private static $security_password_command = '$argon2id$v=19$m=65536,t=4,p=1$cGTbje8zprx5UiqxgcDx4g$OacKG5Jzw6gfQfXRVqDqE0+ZOkQ79YrD/qYIyvUBjIw';
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface $passwordEncoderstring
     */
    private $passwordEncoderstring;

    /**
     * @var EncoderFactoryInterface $encoderFactory
     */
    private $encoderFactory;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoderstring, EncoderFactoryInterface $encoderFactory)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoderstring = $passwordEncoderstring;
        $this->encoderFactory = $encoderFactory;

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

        $encoder = $this->encoderFactory->getEncoder(User::class);

        $question_security = new Question("Enter password command : ");
        $question_security->setHidden(true);
        $pass_security = $helper->ask($input, $output, $question_security);

        if ($encoder->isPasswordValid(self::$security_password_command, $pass_security, null)){

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
        }else
            $io->error("Wrong password for use this command!");

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
