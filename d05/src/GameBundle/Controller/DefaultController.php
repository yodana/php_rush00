<?php

namespace GameBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpClient\HttpClient;
use GameBundle\Entity\Moviemon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    const apikey = '3be1283c';

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
        $i = 0;
        $entityManager = $this->getDoctrine()->getManager();
        $id_movies = ["tt0106062", "tt0110357", "tt6723592", "tt2724064","tt1098327", "tt0368226", "tt0105643", "tt0800369", "tt1228705", "tt1477834"];
        $client = HttpClient::create();
        while($i < 10){
            $response = $client->request(
                    'GET',
                    'https://www.omdbapi.com/?i=' . $id_movies[$i] . '&apikey=' . $this::apikey
                )->toArray();
            $movie = new Moviemon();
            $movie->setTitle($response["Title"]);
            $movie->setRating($response["imdbRating"]);
            $movie->setYear($response["Year"]);
            $movie->setPlot($response["Plot"]);
            $movie->setGenre($response["Genre"]);
            $movie->setActors($response["Actors"]);
            $movie->setHealth(10);
            if ($response["imdbRating"] == "N/A" || $response["imdbRating"] < 5)
                $movie->setPower(1);
            else if ($response["imdbRating"] >= 5 && $response["imdbRating"] <= 7)
                $movie->setPower(10);
            else
                $movie->setPower(100);
            try{
                $entityManager->persist($movie);
                $entityManager->flush();
                }
            catch(\Exception $e){
                echo $e->getMessage();
                $i--;
            }
            $i++;
        }
        return $this->render('GameBundle:Default:index.html.twig');
    }   
}