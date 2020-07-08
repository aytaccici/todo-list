<?php


namespace App\Service;


use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Webservice\BusinessTaskList;
use App\Webservice\ItTasklist;
use App\Webservice\TaskAdapter;
use Symfony\Component\HttpFoundation\Response;

class TaskServiceFacade
{

    private $providers=[
        BusinessTaskList::class,
        ItTasklist::class
    ];

    private $tasks = array();

    private $repository;



    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    public function check() {

        if (!$this->providers){
            return true;
        }


        foreach ($this->providers as $provider) {

            $instance = new $provider;

            /**
             *  @var TaskAdapter
             */
            $adapter = new TaskAdapter($instance);

            $tasks = $adapter->returnTasks();

            $this->tasks= array_merge($this->tasks, $tasks);
        }

        $this->persistData();
        return new Response('OK');
    }


    private function persistData(){

        foreach ($this->tasks as $task) {
            $t= new Task();
            $t->setTitle($task->getTitle());
            $t->setLevel($task->getLevel());
            $t->setDuration($task->getDuration());
            $t->setCreatedAt(new \DateTime());
            $t->setUpdatedAt(new \DateTime());
            $this->repository->addTask($t);
        }
    }

}