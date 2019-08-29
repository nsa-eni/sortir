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

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            // dispatching data from the form
            $params = $searchForm->getData();
            $name = $params['name'];
            $site = $params['site'];

            $dateStart = $params['date_start'];
            $dateEnd = $params['date_end_of_registration'];
            $user = $params['user'];
            $me = $this->getUser();
            $sites = null;
            $eventsFromSearch = null;

            if ($me == $user) {
                $owner = $me;
            }
            $eventEnded = $params['eventEnded'];

            if (is_null($site)) {
                $eventsFromSearch = $entityManager->getRepository(Event::class)
                    ->searchEvent($name, $dateStart, $dateEnd, $owner, $eventEnded);
            } else {
                
                $sites = $entityManager->getRepository(Site::class)
                    ->eventsFromSite($name, $site, $dateStart, $dateEnd, $owner, $eventEnded);
            }

            dump($sites);

            return $this->render('home/index.html.twig', [
                'searchForm' => $searchForm->createView(),
                'eventsFromSearch' => $eventsFromSearch,
                'sites' => $sites
            ]);
        }


        return $this->render('home/index.html.twig', [
            'searchForm' => $searchForm->createView()
        ]);
    }

    /**
     * @Route("event/{id}", name="detail", methods={"GET"})
     */
    public function detail(Event $event, Request $request, EntityManagerInterface $entityManager)
    {
        dump($event);

        return $this->render('event/detail.html.twig', [
            'event' => $event
        ]);
    }
}
