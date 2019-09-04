<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DataController extends AbstractController
{
    /**
     * @Route ("data", name="data", methods={"GET"})
     */
    public function data(){

        return  $this->render("/data/data.html.twig");
    }
}