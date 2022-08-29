<?php

namespace GameBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpClient\HttpClient;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    const apikey = '76c88b32';

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
        $client = HttpClient::create();
        $response = $client->request(
            'GET',
            'https://www.omdbapi.com/?i=tt3896198&apikey=' . $this::apikey
        );
        var_dump($response->getContent());
        return $this->render('GameBundle:Default:index.html.twig');
    }
}
