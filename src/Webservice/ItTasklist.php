<?php


namespace App\Webservice;

class ItTasklist extends TaskList
{

    private const SERVICE_URL= 'http://www.mocky.io/v2/5d47f24c330000623fa3ebfa';
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
        $contentTasks =  json_decode($content);

        $taskEntityList =array();
        foreach ($contentTasks as $task) {


            $taskEntityList[]=  $this->convertToTaskObject($task);

        }
        return $taskEntityList;
    }

    protected function convertToTaskObject($task): Task{

        $entity = new Task();
        $entity->setTitle($task->id);
        $entity->setLevel($task->zorluk);
        $entity->setDuration($task->sure);

        return $entity;
    }
}