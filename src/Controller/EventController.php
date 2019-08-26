<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("/event", name="event")
     */
    public function index()
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);

        return $this->render('event/index.html.twig', ["formEvent" => $form->createView()]);
    }
}
