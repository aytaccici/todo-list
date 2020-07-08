<?php


namespace App\Service;

use App\Repository\TaskRepository;

class Calendar
{

    private const MAXIMUM_TIME_PER_WEEK = 45;
    private  $repository ;

    private $allTask;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    /***
     * Veri tabanında bulunan taskları alır ve sistemde tanımlı bulunan DEVELOPER'lara dağıtır
     * En hızlı olan Developer'a duration'ı ve level'i  en yüksek olan iş verilmeye çalışılır.
     * TErs mantıkla en zayıf developora ise level'i düşük ve duration'ı düşük işler verilerek en hızlı şekilde hareket
     * etmesi amaçlanır
     * @return array
     */

    public function prepare(){


        //Developer Provider
        $developers = array(
            'DEV_5' => '5',
            'DEV_4' => '4',
            'DEV_1' => '1',
            'DEV_2' => '2',
            'DEV_3' => '3',
        );

        $calendar=array();

        $this->allTask = $this->convertToArray($this->repository->getTasksByOrderLevelAndDuration());

        $currentWeek=1;
        do {

            foreach ($developers as $developer) {

                //Developer'in hızına  göre sıralanmış dizinin hangi konumundan iş çekeceğimize karar veriyoruz
                if (in_array($developer,array(5,4))){
                    $sort = 'LAST';
                }else{
                    $sort = 'FIRST';
                }
                $developer_week_table=array();

                $this->getWeekProgram($this->allTask,$sort,$developer_week_table);
                $calendar['DEV_'.$developer]['WEEK_'.$currentWeek] = $developer_week_table;

            }

            $currentWeek++;
        }while(count($this->allTask));


        ksort($calendar);

        return $calendar;

    }


    /**
     * DB'den cekilen verilere iş ağırlığını belirtten weight kontrolünü ekler.
     * Ayrıca dizi keyini DB'deki unique id olarak belirler
     * @param $tasks
     * @return array
     */
    private function convertToArray($tasks){
        $taskArray=array();

        foreach ($tasks as $task) {
            $taskArray[$task->getId()] = [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'duration' => $task->getDuration(),
                'level' => $task->getLevel(),
                'weight'=> $task->getDuration()*$task->getLevel(),
            ];
        }


        $this->aasort($taskArray,'weight');
        return $taskArray;
    }



    function aasort (&$array, $key) {
        $sorter=array();
        $ret=array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii]=$va[$key];
        }
        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii]=$array[$ii];
        }
        $array=$ret;
    }


    /**
     * 45 saati aşmayacak sekilde program oluşturur. Developera eklenecek olan taskları
     * genel diziden siler. Böylelikle aynı taskın başka bir developara atanmasının önüne geçilir
     * @param $allTasks
     * @param $sort
     * @param $inserted
     * @param int $hours
     * @return mixed
     */
    private function getWeekProgram(&$allTasks, $sort, &$inserted, $hours=0){


        if ($hours>=self::MAXIMUM_TIME_PER_WEEK){
            return $inserted;
        }

        $remainingTime= self::MAXIMUM_TIME_PER_WEEK-$hours;
        $maxValue = $this->findProperValue($remainingTime,$removedKey,$sort);

        if ($maxValue){

            $hours += $maxValue['duration'];
            $inserted[] = $maxValue;
            unset($allTasks[$removedKey]);
            return $this->getWeekProgram($allTasks,$sort,$inserted,$hours);
        }


    }


    private function findProperValue($needTime,&$removedKey,$sortBy){
        $sortedArray = $this->arraySortMultiple($this->allTask,array('level','duration'));

        if ($sortBy=='FIRST'){
            $sortedArray= array_reverse($sortedArray);
        }

        $loversThanRequired = array_filter($sortedArray, function ($row) use ($needTime) {
            return $row['duration'] <= $needTime;
        });


        $loversThanRequired= $this->detectKeys($loversThanRequired);

        $removedKey =array_key_first($loversThanRequired);

        if (!isset($loversThanRequired[$removedKey])){
            $removedKey=null;
            return [];
        }
        return $loversThanRequired[$removedKey];
    }

    private function detectKeys($loversThanRequired){

        $items=array();
        foreach ($loversThanRequired as $item) {
            $items[$item['id']] = $item;
        }

        return  $items;
    }

    private function arraySortMultiple($array, $sortBy=array(), $Sort = SORT_REGULAR) {
        if (is_array($array) && count($array) > 0 && !empty($sortBy)) {
            $Map = array();
            foreach ($array as $Key => $Val) {
                $Sort_key = '';
                foreach ($sortBy as $Key_key) {
                    $Sort_key .= $Val[$Key_key];
                }
                $Map[$Key] = $Sort_key;
            }
            asort($Map, $Sort);
            $Sorted = array();
            foreach ($Map as $Key => $Val) {
                $Sorted[] = $array[$Key];
            }
            return array_reverse($Sorted);
        }
        return $array;
    }

}