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
use Twig\Node\EmbedNode;

/**
 * Class EventController
 * @package App\Controller
 * @Route("user")
 */
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

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @Route("/subscribe/{id}", name="subscribe", methods={"GET"})
     */
    public function subscribe(Event $event, Request $request, EntityManagerInterface $entityManager) {
        $user = $this->getUser();
        $user->addEvents($event);
        $event->addSubscribersUsers($user);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Event $event
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @Route("/unsubscribe/{id}", name="unsubscribe", methods={"GET"})
     */
    public function unSubscribe(Event $event, Request $request, EntityManagerInterface $entityManager) {
        $user = $this->getUser();
        $user->removeEvents($event);
        $event->removeSubscribersUsers($user);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Event $event
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @Route("/cancel/{id}", name="cancelEvent", methods={"GET"})
     */
    public function cancel(Event $event, Request $request, EntityManagerInterface $entityManager) {


        return $this->render('event/cancel.html.twig', ['event' => $event]);
    }

    public function modify(Event $event, Request $request, EntityManagerInterface $entityManager) {
        return $this->render('event/modify.html.twig', ['event' => $event]);
    }
}
