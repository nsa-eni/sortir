<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SiteController
 * @package App\Controller
 * @Route("/admin/site/")
 */
class SiteController extends AbstractController
{
    /**
     * @Route("index", name="site_index", methods={"GET","POST"})
     */
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $site->setName(mb_strtoupper($site->getName()));
            $this->addFlash("success", "Le nouveau site a bien été enregistré !");
            $entityManager->persist($site);
            $entityManager->flush();


            return $this->redirectToRoute('site_index');
        }
        //récup info barre recherche
        $data = $request->query->get('search');
        if (!$data) {
            $req = $entityManager->getRepository('App:Site')->findSites();
        }
        //si utilisation barre de recherche
        if ($data) {
            $req = $entityManager->getRepository('App:Site')->SiteNameContain($data);
        }
        return $this->render('site/index.html.twig', ["tabSite" => $req,  'formSite' => $form->createView()]);
    }

    /**
     * @Route("/{id}/edit", name="site_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Site $site): Response
    {
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $site->setName(mb_strtoupper($site->getName()));
            $this->addFlash("success", "Le site a bien été modifié !");
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('site_index');
        }

        return $this->render('site/edit.html.twig', [
            'site' => $site,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="site_delete", methods={"DELETE"})
     */
    public function delete(EntityManagerInterface $entityManager,Request $request, Site $site): Response
    {
        if ($this->isCsrfTokenValid('delete'.$site->getId(), $request->request->get('_token'))) {
            $this->addFlash("success", "Le site a bien été effacé !");
            $entityManager->remove($site);
            $entityManager->flush();
        }

        return $this->redirectToRoute('site_index');
    }
}
