<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
class WelcomeController extends AbstractController
{
    /**
     * @Route ("welcome", name="welcome", methods={"GET"})
     */
    public function cgu(){

        return  $this->render("/welcome/welcome.html.twig");
    }
}