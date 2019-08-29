<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\State;
use App\Form\EventType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class EventController extends AbstractController
{
    /**
     * @Route("/event", name="event", methods={"GET", "POST"})
     */
    public function index(EntityManagerInterface $entityManager, Request $request, Security $security)
    {

        if ($request->isXmlHttpRequest()){
            return $this->getPostalCodeByCityId($request, $entityManager);
        }else{
            $user = $security->getUser();

            $event = new Event();
            $event->setUser($user);
            $event->setSite($user->getSite());
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

    public function getPostalCodeByCityId(Request $request, EntityManager $entityManager)
    {
        $cityRepo = $entityManager->getRepository(City::class);
        $city = $cityRepo->find(intval($request->request->get('city_id')));
        return new Response(json_encode([
            'zip_code'=> $city->getZipCode()
        ]), 200);
    }
}
