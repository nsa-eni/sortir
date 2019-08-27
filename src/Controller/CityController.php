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
            $city->setName(strtoupper($city->getName()));
            $entityManager->persist($city);
            $entityManager->flush();

            return $this->redirectToRoute('city_index');
        }
        //récup info barre recherche
        $data = $request->query->get('search');
        if (!$data) {
            $req = $entityManager->getRepository('App:City')->findAll();
        }
        //si utilisation barre de recherche
        if ($data) {
            $req = $entityManager->getRepository('App:City')->CityNameContain($data);
        }
        return $this->render('city/index.html.twig', ["tabCity" => $req,  'formCity' => $form->createView()]);
    }

    /**
     * @Route("/{id}", name="city_show", methods={"GET"})
     */
    public function show(City $city): Response
    {
        return $this->render('city/show.html.twig', [
            'city' => $city,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="city_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, City $city): Response
    {
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
    public function delete(Request $request, City $city): Response
    {
        if ($this->isCsrfTokenValid('delete'.$city->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($city);
            $entityManager->flush();
        }

        return $this->redirectToRoute('city_index');
    }
}
