<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\State;
use App\Form\EventType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

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

        if ($request->isXmlHttpRequest()) {
            return $this->getPostalCodeByCityId($request, $entityManager);
        } else {
            $user = $security->getUser();

            $event = new Event();
            $event->setUser($user);
            $event->setSite($user->getSite());
            $form = $this->createForm(EventType::class, $event);
            $form->add('publier', SubmitType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $req = $request->request->get('event');
                $stateRepo = $entityManager->getRepository(State::class);
                if (isset($req['save'])) {
                    $state = $stateRepo->findOneBy(['name' => 'Créée']);
                    $event->setState($state);
                } elseif (isset($req['publier'])) {
                    $state = $stateRepo->findOneBy(['name' => 'Ouverte']);
                    $event->setState($state);
                } elseif (isset($req['cancel'])) {
                    return $this->redirectToRoute("home");
                }

                $entityManager->persist($event);
                $entityManager->flush();
                return $this->redirectToRoute('home');
            }
            return $this->render('event/index.html.twig', ["formEvent" => $form->createView()]);
        }
    }

    public function getPostalCodeByCityId(Request $request, EntityManager $entityManager)
    {
        $cityRepo = $entityManager->getRepository(City::class);
        $city = $cityRepo->find(intval($request->request->get('city_id')));
        return new Response(json_encode([
            'zip_code' => $city->getZipCode()
        ]), 200);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @Route("/subscribe/{id}", name="subscribe", methods={"GET"})
     */
    public function subscribe(Event $event, Request $request, EntityManagerInterface $entityManager)
    {
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
    public function unSubscribe(Event $event, Request $request, EntityManagerInterface $entityManager)
    {
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
     * @Route("/cancel/{id}", name="cancel", methods={"GET"})
     */
    public function cancel(Event $event, Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $entityManager->initializeObject($event->getLocation());
        return $this->render('event/cancel.html.twig', ['event' => $event, 'user' => $user]);
    }

    /**
     * @param Event $event
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @Route("/cancelEvent/{id}", name="cancelEvent", methods={"GET"})
     */
    public function cancelEvent(Event $event, Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $owner = $event->getUser();
        $entityManager->initializeObject($event->getState());
        $event->setState(null);

        if ($user->getId() == $owner->getId() or $user->getRoles()[0] == "ROLE_ADMIN") {
            $entityManager->remove($event);
            $entityManager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @param Event $event
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/modify/{id}", name="modifyEvent", methods={"GET", "POST"})
     */
    public function modify(Event $event, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(EventType::class, $event);

        if ($event->getState()->getName() == "Créée") {
            $form->add('publier', SubmitType::class);
        }
        $form->add('supprimer', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $req = $request->request->get('event');
            $stateRepo = $entityManager->getRepository(State::class);
            if (isset($req['save'])) {
                $state = $stateRepo->findOneBy(['name' => 'Créée']);
            } elseif (isset($req['publier'])) {
                $state = $stateRepo->findOneBy(['name' => 'Ouverte']);
            } elseif (isset($req['supprimer'])) {
                return $this->redirectToRoute("cancelEvent", ['id' => $event->getId()]);
            } elseif (isset($req['cancel'])) {
                return $this->redirectToRoute("home");
            }
            $event->setState($state);
            $entityManager->persist($event);
            $entityManager->flush();
            return $this->redirectToRoute("home");
        }

        return $this->render('event/modify.html.twig', ["formEvent" => $form->createView(), 'event' => $event]);
    }

    /**
     * @param Event $event
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/publish/{id}", name="publish", methods={"GET"})
     */
    public function publish(Event $event, Request $request, EntityManagerInterface $entityManager)
    {
        $state = new State();
        $state->setName("Ouverte");
        $event->setState($state);
        $entityManager->persist($event);
        $entityManager->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
