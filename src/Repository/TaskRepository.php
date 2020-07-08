<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }


    public function addTask($task): void
    {
        if (!$task instanceof Task) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($task)));
        }

        $this->_em->persist($task);
        $this->_em->flush();
    }





    public function getTasksByOrderLevelAndDuration()
    {

        return $this->createQueryBuilder('t')
            ->addOrderBy('t.level', 'ASC')
            ->addOrderBy('t.duration', 'ASC')
            ->getQuery()
            ->getResult();

    }


}
