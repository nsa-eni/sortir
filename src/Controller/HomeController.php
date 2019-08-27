<?php

namespace App\Controller;

use App\Form\HomeType;
use App\Entity\Event;
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
            $name = $params['search'];
            $city = $params['city'];
            $dateStart = $params['date_start'];
            $dateEnd = $params['date_end'];
            $owner = $params['owner'];
            $subscribed = $params['subscribed'];
            $notSubscribed = $params['notSubscribed'];
            $eventEnded = $params['eventEnded'];

            $eventsFromSearch = $entityManager->getRepository(Event::class)
                ->searchEvent($name, $city, $dateStart, $dateEnd, $owner, $eventEnded);

            $user = $this->getUser();

            if (!is_null($subscribed)) {
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
            }

            return $this->render('home/index.html.twig', [
                'SearchForm' => $searchForm->createView(),
                $eventsFromSearch
            ]);
        }


        return $this->render('home/index.html.twig', [
            'searchForm' => $searchForm->createView()
        ]);
    }
}
