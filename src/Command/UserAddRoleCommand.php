<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use http\Exception\InvalidArgumentException;
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

        $helper->ask($input, $output, $this->createSecurityPasswordCommandQuestion());

        $email = $helper->ask($input, $output, $this->createEmailQuestion());

        $password = $helper->ask($input, $output, $this->createPasswordQuestion());

        if ($this->createUser($email, $password)){

            $message1 = sprintf("Email: %s", $email);
            $message2 = sprintf("Password: %s", $password);
            $output->writeln($message1);
            $output->writeln($message2);
            $io->success('User admin has been created');

        }else
            $io->error("Process failed!");
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

    /**
     * @return Question
     */
    private function createEmailQuestion(): Question
    {
        $question = new Question("Enter email: ");
        return $question->setValidator(function ($value) {
            if (is_null($value) || '' === trim($value)) {
                throw new \InvalidArgumentException('The email must not be empty.');

            }
            if (!$this->isValidEmail($value)){
                throw new \InvalidArgumentException('The email is not valid.');

            }

            return $value;
        })->setMaxAttempts(20);
    }

    /**
     * @return Question
     */
    private function createPasswordQuestion(): Question
    {
        $question = new Question("Enter password: ");
        return $question->setValidator(function ($value) {

            if ('' === trim($value) || is_null($value)) {
                throw new \InvalidArgumentException('The password must not be empty.');
            }

            return $value;
        })->setMaxAttempts(20);
    }

    /**
     * @return Question
     */
    private function createSecurityPasswordCommandQuestion() :Question
    {
        $question = new Question("Enter password command : ");
        return $question->setValidator(function ($value) {
            if ('' === trim($value)) {
                throw new \InvalidArgumentException('The password must not be empty.');
            }
            if (!$this->encoderFactory->getEncoder(User::class)->isPasswordValid(self::$security_password_command, $value, null)){
                throw new \InvalidArgumentException('Invalid password.');

            }

            return $value;
        })->setHidden(true)->setMaxAttempts(3);
    }

    /**
     * @param string $email
     * @return bool
     */
    private function isValidEmail(string $email)
    {
        return (boolean) filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
