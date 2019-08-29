<?php

namespace App\Controller;

use App\Form\HomeType;
use App\Entity\Event;
use App\Entity\Site;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET", "POST"})
     */
    public function index(EntityManagerInterface $entityManager, Request $request)
    {
        // creating the form
        $searchForm = $this->createForm(HomeType::class);
        $searchForm->handleRequest($request);

        if($searchForm->isSubmitted() && $searchForm->isValid()) {
            // dispatching data from the form
            $params = $searchForm->getData();
            $name = $params['name'];
            $site = $params['site'];

            $dateStart = $params['date_start'];
            $dateEnd = $params['date_end_of_registration'];
            $user = $params['user'];
            $me = $this->getUser();

            $number = $entityManager->getRepository('App\Entity\Event')->getSubscribers();

            if ($me == $user) {
                $owner = $me;
            }
            //$subscribed = $params['subscribed'];
            //$notSubscribed = $params['notSubscribed'];
            $eventEnded = $params['eventEnded'];

            if (is_null($site)) {

                $eventsFromSearch = $entityManager->getRepository(Event::class)
                    ->searchEvent($name, $dateStart, $dateEnd, $owner, $eventEnded);

            } else {
                $eventsFromSearch = $entityManager->getRepository(Site::class)
                    ->eventsFromSite($name, $site, $dateStart, $dateEnd, $user, $eventEnded);
            }

            $user = $this->getUser();

            /*if (!is_null($subscribed)) {
                $entityManager->initializeObject($user->getEvents());

                $events = $user->getEvents();
            }

            if (!is_null($notSubscribed)) {
                $entityManager->initializeObject($user->getEvents());
                $allEvents = $entityManager->getRepository('Event')
                    ->findAll();
                $events = $user->getEvents();
                $eventsNotIn = [];
                foreach($allEvents as $e) {
                    if (!in_array($e, $events)) {
                        array_push($eventsNotIn, $e);
                    }
                }
            }*/

            dump($eventsFromSearch);

            return $this->render('home/index.html.twig', [
                'searchForm' => $searchForm->createView(),
                'eventsFromSearch' => $eventsFromSearch
            ]);
        }


        return $this->render('home/index.html.twig', [
            'searchForm' => $searchForm->createView()
        ]);
    }
}
