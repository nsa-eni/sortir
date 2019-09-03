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
    private static $calendar_names =
        ['YEAR', 'QUARTER', 'DAY', 'HOUR', 'MINUTE', 'WEEK', 'SECOND', 'YEAR_MONTH', 'DAY_HOUR', 'DAY_MINUTE', 'DAY_SECOND', 'HOUR_MINUTE', 'HOUR_SECOND', 'MINUTE_SECOND'];
    private $entityManager;

    private $execute_calendar = [];

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
        if ($event_type == 1){
            $execute_every = $helper->ask($input, $output, $this->createExecuteEveryQuestion());
            $execute_interval = $helper->ask($input, $output, $this->createExecuteIntervalQuestion());
            $execute_start_time = $helper->ask($input, $output, $this->createExecuteStartTimeQuestion());
            $execute_end_time = $helper->ask($input, $output, $this->createExecuteEndTimeQuestion());

            $this->execute_calendar['execute_every'] = self::$calendar_names[$execute_every];
            $this->execute_calendar['interval'] = (int)$execute_interval;

            if (!is_null($execute_start_time) && trim($execute_start_time) !== "")
                $this->execute_calendar['start'] = $execute_start_time;
            if (!is_null($execute_end_time) && trim($execute_end_time) !== "")
                $this->execute_calendar['end'] = $execute_end_time;


        }else{
            $execute_time = $helper->ask($input, $output, $this->createExecuteAtQuestion());
            $this->execute_calendar['execute_at'] = $execute_time;

        }


        $definition = $helper->ask($input, $output, $this->createDefinitionQuestion());
        $completion_conserve = $helper->ask($input, $output, $this->createOnCompletionConserveQuestion());


        $this->createQuery($name, self::$status[$status], self::$eventType[$event_type], $this->execute_calendar, $definition, self::$completion_conserve[$completion_conserve]);

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
    private function createExecuteAtQuestion(): Question
    {
        $question = new Question("Execute at (Enter date >> ".self::$dateFormat."): ");
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

    private function createExecuteStartTimeQuestion(): Question
    {
        $question = new Question("Start at (Enter date >> ".self::$dateFormat."): ");
        return $question->setValidator(function ($value) {
            if (!is_null($value) && '' !== trim($value)){
                if (!$this->checkDate($value)){
                    throw new \InvalidArgumentException('Invalid format date.');

                }
            }
            return $value;
        })->setMaxAttempts(20);
    }

    private function createExecuteEndTimeQuestion(): Question
    {
        $question = new Question("End at (Enter date >> ".self::$dateFormat."): ");
        return $question->setValidator(function ($value) {
            if (!is_null($value) && '' !== trim($value)){
                if (!$this->checkDate($value)){
                    throw new \InvalidArgumentException('Invalid format date.');

                }
            }
            return $value;
        })->setMaxAttempts(20);
    }

    private function createExecuteEveryQuestion(): Question
    {
        $question = new ChoiceQuestion(
            'Execute every (defaults to YEAR): ',
            self::$calendar_names,
            0
        );
        return $question->setValidator(function ($value) {
            if (is_null($value) || '' === trim($value)) {
                throw new \InvalidArgumentException('The argument must not be empty.');
            }
            return $value;
        })->setMaxAttempts(20);
    }

    private function createExecuteIntervalQuestion(): Question
    {
        $question = new Question("Interval number: ");
        return $question->setValidator(function ($value) {
            if (is_null($value) || '' === trim($value)) {
                throw new \InvalidArgumentException('The argument must not be empty.');
            }
            if (!is_numeric($value)){
                throw new \InvalidArgumentException('The argument must be a number.');
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

        $sql = "CREATE DEFINER=`root`@`localhost` EVENT `$name` ON SCHEDULE ";
        if ($event_type === 'ONE TIME'){
            $time = \DateTime::createFromFormat(self::$dateFormat, $execute_time['execute_at']);
            $execute_at = $time->format('Y-m-d H:i:s');
            $sql .= "AT '$execute_at'";

        }elseif ($event_type === 'RECURRING'){
            $sql .= " EVERY ".$execute_time['interval']." ".$execute_time['execute_every'];
            if (isset($execute_time['start'])){
                $time = \DateTime::createFromFormat(self::$dateFormat, $execute_time['start']);
                $start = $time->format('Y-m-d H:i:s');
                $sql .= " STARTS '$start'";
            }

            if (isset($execute_time['end'])){
                $time = \DateTime::createFromFormat(self::$dateFormat, $execute_time['end']);
                $end = $time->format('Y-m-d H:i:s');
                $sql .= "ENDS '$end'";
            }

        }
        $sql .= " ON COMPLETION $completion_conserve $status DO $definition";
        $this->entityManager->getConnection()->exec($sql);
        $this->entityManager->getConnection()->exec("SET GLOBAL event_scheduler = 1");
        $this->saveEventNameInFile($name);
    }

    private function checkDate(string $date)
    {
        $d = \DateTime::createFromFormat(self::$dateFormat, $date);
        return $d && $d->format(self::$dateFormat) === $date;
    }

    private function saveEventNameInFile(string $event_name)
    {
        $events_data = json_decode(file_get_contents(__DIR__.'/database-events.json'));
        $events_data[] = $event_name;

        $handle = fopen(__DIR__.'/database-events.json', 'w');
        fputs($handle, json_encode($events_data));
        fclose($handle);
    }
}
