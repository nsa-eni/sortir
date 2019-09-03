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
     * @return Event[] returns an array of Event objects
     * @throws \Exception
     */
    public function searchEvent($name, $dateStart, $dateEnd, $user, $eventEnded, $site, $myId, $subscribed, $notsubscribed)
    {
        $name = explode(' ', $name);
        $dateNow = new \DateTime('now');
        $req = $this->createQueryBuilder('e')->addSelect('state')
            ->leftJoin('e.state', 'state');

        if (!is_null($site)) {
            $req->leftJoin('e.site', 'site');
        }

        if ($subscribed and $notsubscribed) {

        } else {
            if ($subscribed) {
               $req->join('e.subscribers_users', 'su')
                    ->andWhere('su.id = :me')
                    ->setParameter('me', $myId);
            }

            if ($notsubscribed) {
                $req2 = $this->createQueryBuilder('event')->select('event.id')
                    ->leftJoin('event.subscribers_users', 'subs')
                    ->where('subs.id = :me');

                $req->leftJoin('e.subscribers_users', 'sub')
                    ->where('e.id not in (' . $req2 . ')')->setParameter('me', $myId);


            }
        }

        if (!is_null($dateStart) && !is_null($dateEnd) and $dateStart and $dateEnd) {
            $req->andWhere('e.date_start BETWEEN :date_start AND :date_end_of_registration')
                ->setParameter('date_start', $dateStart)
                ->setParameter('date_end_of_registration', $dateEnd);
        }

        if ($user) {
            $req->andWhere('e.user = :me')->setParameter('me', $myId);
        }

        if (!is_null($eventEnded) and $eventEnded) {
            $req->andWhere(':dateNow >= e.date_end_of_registration')->setParameter('dateNow', $dateNow);
        }

        if (!is_null($name) and !empty($name[0])) {
            foreach ($name as $n)
                $req->andWhere('e.name like :n')
                    ->setParameter('n', '%' . $n . '%');
        }

        return $req->getQuery()->getResult();
    }

    public function getSubscribers()
    {
        return $this->createQueryBuilder('e')
            ->addSelect('user')
            ->leftJoin('e.subscribers_users', 'user')
            ->getQuery()
            ->getResult();
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
