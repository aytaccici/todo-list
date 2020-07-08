<?php


namespace App\Webservice;


class TaskAdapter implements  WebService
{

    private $serviceType;

    /**
     * TaskAdapter constructor.
     * @param $serviceType
     */
    public function __construct(TaskList $serviceType)
    {
        $this->serviceType = $serviceType;
    }

    /**
     * Returns  array of Tasks
     * @return array
     */
    public function returnTasks(): array
    {
       return $this->serviceType->getTasks();


    }


}