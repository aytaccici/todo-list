<?php


namespace App\Webservice;


class BusinessTaskList extends TaskList
{

    private const SERVICE_URL= 'http://www.mocky.io/v2/5d47f235330000623fa3ebf7';

    public function getTasks(): array
    {

        $response = $this->client->request(
            'GET',
            self::SERVICE_URL
        );

        if ($response->getStatusCode()<>200){
            throw new \Exception('Service is down!');
        }

        return $this->convert($response->getContent());
    }

    public function convert($content): array
    {
        $contentTasks =  json_decode($content,true);

        $taskEntityList =array();
        foreach ($contentTasks as $task) {

            $taskEntityList[]= $this->convertToTaskObject($task);


        }
        return $taskEntityList;
    }

    protected function convertToTaskObject($task): Task{
        $title = key($task);
        $entity = new Task();
        $entity->setTitle($title);
        $entity->setLevel($task[$title]['level']);
        $entity->setDuration($task[$title]['estimated_duration']);
        return $entity;
    }

}