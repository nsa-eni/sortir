<?php


namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SiteController
 * @package App\Controller
 * @Route("/admin/site/")
 */class SiteController extends Controller
{
    /**
     * @Route("search", name="site_search", methods={"GET"})
     */
    public function listSite(EntityManagerInterface $entityManager, Request $request){
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        //récup info barre recherche
        $data = $request->query->get('search');
        if(!$data) {
            $req = $entityManager->getRepository('App:Site')->findAll();
        }
        //si utilisation barre de recherche
        if ($data){
            $req = $entityManager->getRepository('App:Site')->CityNameContain($data);
        }
        return $this->render('site/sitesearch.html.twig', ["tabSite" => $req,"formSite" => $form->createView()]);
    }
    }
