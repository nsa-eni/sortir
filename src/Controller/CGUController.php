<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
class CGUController extends AbstractController
{
    /**
     * @Route ("cgu", name="cgu", methods={"GET"})
     */
    public function cgu(){

        return  $this->render("/cgu/cgu.html.twig");
    }
}