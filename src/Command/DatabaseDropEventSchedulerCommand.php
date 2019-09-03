<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseDropEventSchedulerCommand extends Command
{
    /**
     * @var string $defaultName
     */
    protected static $defaultName = 'database:drop:event-scheduler';

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct(self::$defaultName);
    }

    protected function configure()
    {
        $this
            ->setDescription("Delete a event scheduler from database")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        $eventName = $helper->ask($input, $output, $this->createDropEventQuestion());

        $this->entityManager->getConnection()->exec("DROP EVENT IF EXISTS `$eventName`");

        $io->success("The event $eventName has been deleted");
    }

    private function createDropEventQuestion(): Question
    {
        $events_name = json_decode(file_get_contents(__DIR__.'/database-events.json'), true);
        $question = new Question('Please enter the name of a event: ');
        $question->setAutocompleterValues($events_name);

        return $question;
    }
}
