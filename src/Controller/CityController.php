<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
* Class CityController
 * @package App\Controller
* @Route("/admin/city/")
*/
class CityController extends AbstractController
{
    /**
     * @Route("index", name="city_index", methods={"GET","POST"})
     */
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $city->setName(mb_strtoupper($city->getName()));
            $this->addFlash("success", "La nouvelle ville a bien été enregistrée !");
            $entityManager->persist($city);
            $entityManager->flush();

            return $this->redirectToRoute('city_index');
        }
        //récup info barre recherche
        $data = $request->query->get('search');
        if (!$data) {
            $req = $entityManager->getRepository('App:City')->findCities();
        }
        //si utilisation barre de recherche
        if ($data) {
            $req = $entityManager->getRepository('App:City')->CityNameContain($data);
        }
        return $this->render('city/index.html.twig', ["tabCity" => $req,  'formCity' => $form->createView()]);
    }

    /**
     * @Route("/{id}/edit", name="city_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, City $city): Response
    {
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $city->setName(mb_strtoupper($city->getName()));
            $this->addFlash("success", "La ville a bien été modifiée !");
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('city_index');
        }

        return $this->render('city/edit.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="city_delete", methods={"DELETE"})
     */
    public function delete(EntityManagerInterface $entityManager,Request $request, City $city): Response
    {
        if ($this->isCsrfTokenValid('delete'.$city->getId(), $request->request->get('_token'))) {
            $this->addFlash("success", "La ville a bien été effacée !");
            $entityManager->remove($city);
            $entityManager->flush();
        }

        return $this->redirectToRoute('city_index');
    }
}
