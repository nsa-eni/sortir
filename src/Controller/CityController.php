<?php


namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SiteController
 * @package App\Controller
 * @Route("/admin/city/")
 */class CityController extends Controller
{
    /**
     * @Route("search", name="city_search", methods={"GET"})
     */
    public function listSite(EntityManagerInterface $entityManager, Request $request){
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        //récup info barre recherche
        $data = $request->query->get('search');
        if(!$data) {
            $req = $entityManager->getRepository('App:City')->findAll();
        }
        //si utilisation barre de recherche
        if ($data){
            $req = $entityManager->getRepository('App:City')->CityNameContain($data);
        }
        return $this->render('city/citysearch.html.twig', ["tabCity" => $req,"formCity" => $form->createView()]);
    }
}