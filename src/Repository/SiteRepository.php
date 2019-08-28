<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Site|null find($id, $lockMode = null, $lockVersion = null)
 * @method Site|null findOneBy(array $criteria, array $orderBy = null)
 * @method Site[]    findAll()
 * @method Site[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Site::class);
    }

    public function SiteNameContain($data)
    {
        return $this->createQueryBuilder('s')
            ->Where('s.name LIKE :data')
            ->setParameter('data', '%'.$data.'%')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $site
     * @return mixed
     */
    public function eventsFromSite($name, $site, $dateStart, $dateEnd, $owner, $eventEnded) {
        $name = explode(' ', $name);
        $req = $this->createQueryBuilder('s');
        $dateNow = new \DateTime('now');

        $req->addSelect('event')
            ->leftJoin('s.events', 'event');

            if (!is_null($name)) {
                foreach($name as $n)
                $req->andWhere('event.name like :n')
                    ->setParameter('n', $n);
            }

             if (!is_null($dateStart) && !is_null($dateEnd)) {
                 $req->andWhere('event.date_start BETWEEN :date_start AND :date_end_of_registration')
                     ->setParameter('date_start', $dateStart)
                     ->setParameter('date_end_of_registration', $dateEnd);
             }

              if (!is_null($owner)) {
                  $req->andWhere('event.owner = :owner')->setParameter('owner', $owner);
              }

        if (!is_null($eventEnded)) {
            $req->andWhere(':dateNow >= event.date_end_of_registration')->setParameter('dateNow', $dateNow);
        }

        return $req->getQuery()->getResult();
    }

}
