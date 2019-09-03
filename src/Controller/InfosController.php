<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

class InfosController extends AbstractController
{
    /**
     * @Route ("infos", name="infos", methods={"GET"})
     */
    public function infos()
    {

        return $this->render("/infos/infos.html.twig");
    }
}