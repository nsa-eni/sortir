<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FAQController extends AbstractController
{
    /**
     * @Route ("faq", name="faq", methods={"GET"})
     */
    public function accueil(){

        return  $this->render("/faq/faq.html.twig");
    }
}