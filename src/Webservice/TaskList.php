<?php


namespace App\Webservice;
use Symfony\Component\HttpClient\HttpClient;

abstract  class TaskList
{
    protected $client;


    public function __construct()
    {
        $this->client = HttpClient::create();
    }

    abstract function convert($content) : array;

    protected abstract function convertToTaskObject($content) : Task;

    public abstract function getTasks():array;
}