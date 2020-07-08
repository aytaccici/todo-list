<?php

namespace App\Command;

use App\Repository\TaskRepository;
use App\Service\TaskServiceFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CheckTask extends Command
{
    protected static $defaultName = 'app:check-tasks';

    private $repository;


    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct(self::$defaultName);
    }

    protected function configure()
    {
        $this
            ->setDescription('Check task list and save them to database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);

        $taskFacade = new TaskServiceFacade($this->repository);
        $taskFacade->check();
        $io->success('All Tasks added to Database successfully.');

        return 0;
    }
}
