<?php

namespace GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/options/")
     */
    public function indexAction()
    {
        return $this->render('GameBundle:Default:index.html.twig');
    }

     /**
     * @Route("/game/")
     */
    public function getNewGame()
    {
        
        return $this->render('GameBundle:Default:index.html.twig');
    }
}
