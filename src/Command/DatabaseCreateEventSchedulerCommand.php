<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseCreateEventSchedulerCommand extends Command
{
    protected static $defaultName = 'database:create:event-scheduler';
    private static $status = ['ENABLE', 'DISABLE', 'SLAVESIDE_DISABLED'];
    private static $eventType = ['ONE TIME', 'RECURRING'];
    private static $dateFormat = 'd/m/Y H:i:s';
    private static $completion_conserve = ['NOT PRESERVE', 'PRESERVE'];
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct(self::$defaultName);
    }

    protected function configure()
    {
        $this
            ->setDescription('Create a event scheduler on database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        $name = $helper->ask($input, $output, $this->createNameQuestion());
        $status = $helper->ask($input, $output, $this->createStatusQuestion());
        $event_type = $helper->ask($input, $output, $this->createEventTypeQuestion());
        $execute_time = $helper->ask($input, $output, $this->createExecuteEveryQuestion());
        $definition = $helper->ask($input, $output, $this->createDefinitionQuestion());
        $completion_conserve = $helper->ask($input, $output, $this->createOnCompletionConserveQuestion());

        $this->createQuery($name, self::$status[$status], self::$eventType[$event_type], $execute_time, $definition, self::$completion_conserve[$completion_conserve]);

        $io->success('Event has been created');
    }


    private function createNameQuestion(): Question
    {
        $question = new Question("Name: ");
        return $question->setValidator(function ($value) {
            if (is_null($value) || '' === trim($value)) {
                throw new \InvalidArgumentException('The argument must not be empty.');
            }
            return $value;
        })->setMaxAttempts(20);
    }

    private function createStatusQuestion(): Question
    {
        $question = new ChoiceQuestion(
            'Please select the status (defaults to ENABLED): ',
            self::$status,
            0
        );
        return $question->setValidator(function ($value) {
            if (is_null($value) || '' === trim($value)) {
                throw new \InvalidArgumentException('The argument must not be empty.');
            }
            return $value;
        })->setMaxAttempts(20);
    }

    /**
     * @return Question
     */
    private function createEventTypeQuestion(): Question
    {
        $question = new ChoiceQuestion(
            'Please select event type (defaults to ONE TIME): ',
            self::$eventType,
            0
        );
        return $question->setValidator(function ($value) {
            if (is_null($value) || '' === trim($value)) {
                throw new \InvalidArgumentException('The argument must not be empty.');
            }
            return $value;
        })->setMaxAttempts(20);
    }

    /**
     * @return Question
     */
    private function createExecuteEveryQuestion(): Question
    {
        $question = new Question("Execute every (Enter date >> ".self::$dateFormat."): ");
        return $question->setValidator(function ($value) {
            if (is_null($value) || '' === trim($value)) {
                throw new \InvalidArgumentException('The date must not be empty.');
            }

            if (!$this->checkDate($value)){
                throw new \InvalidArgumentException('Invalid format date.');

            }

            return $value;
        })->setMaxAttempts(20);


    }

    /**
     * @return Question
     */
    private function createDefinitionQuestion()
    {
        $question = new Question("Definition: ");
        return $question->setValidator(function ($value) {
            if (is_null($value) || '' === trim($value)) {
                throw new \InvalidArgumentException('The argument must not be empty.');
            }
            return $value;
        })->setMaxAttempts(20);
    }

    private function createOnCompletionConserveQuestion()
    {
        $question = new ChoiceQuestion(
            'On Completion Conserve (defaults to NOT PRESERVE): ',
            self::$completion_conserve,
            0
        );
        return $question->setValidator(function ($value) {
            if (is_null($value) || '' === trim($value)) {
                throw new \InvalidArgumentException('The argument must not be empty.');
            }
            return $value;
        })->setMaxAttempts(20);
    }


    private function createQuery($name, $status, $event_type, $execute_time, $definition, $completion_conserve)
    {
        //$sql1 ="CREATE DEFINER=`root`@`localhost` EVENT `eeeee` ON SCHEDULE EVERY 1 DAY STARTS '2019-09-03 00:00:00' ENDS '2019-09-04 00:00:00' ON COMPLETION PRESERVE DISABLE DO SELECT * FROM event";
        $time = \DateTime::createFromFormat(self::$dateFormat, $execute_time);
        $execute_time = $time->format('Y-m-d H:i:s');

        $sql = "CREATE DEFINER=`root`@`localhost` EVENT `$name` ON SCHEDULE ";
        if ($event_type === 'ONE TIME'){

            $sql .= "AT '$execute_time'";

        }
        $sql .= "ON COMPLETION $completion_conserve $status DO $definition";

        $this->entityManager->getConnection()->exec($sql);
    }

    private function checkDate(string $date)
    {
        $d = \DateTime::createFromFormat(self::$dateFormat, $date);
        return $d && $d->format(self::$dateFormat) === $date;
    }
}
