<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\State;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("/event", name="event", methods={"GET", "POST"})
     */
    public function index(EntityManagerInterface $entityManager, Request $request)
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $req = $request->request->get('event');
            $stateRepo = $entityManager->getRepository(State::class);
            if (isset($req['save'])){
                $state = $stateRepo->findOneBy(['name' => 'Créée']);
            }elseif (isset($req['publish'])){
                $state = $stateRepo->findOneBy(['name' => 'Ouverte']);
            }elseif (isset($req['cancel'])){
                return $this->redirectToRoute("home");
            }
            $event->setState($state);
            $entityManager->persist($event);
            $entityManager->flush();
        }
        return $this->render('event/index.html.twig', ["formEvent" => $form->createView()]);
    }
}
