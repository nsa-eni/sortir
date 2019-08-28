<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @param $name
     * @param $city
     * @param $dateStart
     * @param $dateEnd
     * @param $owner
     * @param $eventEnded
     * @return mixed
     * @throws \Exception
     * @return Event[] returns an array of Event objects
     */
    public function searchEvent($name, $site, $dateStart, $dateEnd, $owner, $eventEnded) {
        $name = explode(' ', $name);
        $req = $this->createQueryBuilder('e');
        $dateNow = new \DateTime('now');

        if (!is_null($name)) {
            foreach($name as $n) {
                $req->andWhere('e.name LIKE :n')->setParameter('n', $n);
            }
        }

        if (!is_null($site)) {
            $req->andWhere('e.site like :site')->setParameter('site', $site);
        }

        if (!is_null($dateStart) && !is_null($dateEnd)) {
            $req->andWhere('e.date_start BETWEEN :date_start AND :date_end')
                ->setParameter('date_start', $dateStart)
                ->setParameter('date_end', $dateEnd);
        }

        if (!is_null($owner)) {
            $req->andWhere('e.owner = :owner')->setParameter('owner', $owner);
        }

        if (!is_null($eventEnded)) {
            $req->andWhere(':dateNow >= e.date_end')->setParameter('dateNow', $dateNow);
        }

        return $req->getQuery()->getResult();
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
