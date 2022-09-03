<?php

namespace GameBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpClient\HttpClient;
use GameBundle\Entity\Moviemon;
use GameBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    const apikey = '3be1283c';
    
    public function newUser($name, $health, $power){
        $user = new User();
        $entityManager = $this->getDoctrine()->getManager();
        $user->setUsername($name);
        $user->setPower($power);
        $user->setHealth($health);
        try{
            $entityManager->persist($user);
            $entityManager->flush();
            $message = "User create by success";
            }
        catch(\Exception $e){
            $message = $e->getMessage();
        }
        return $message;
    }
    public function newMovie($title, $rating, $year, $plot, $genre, $actors){
        $entityManager = $this->getDoctrine()->getManager();
        $movie = new Moviemon();
        $movie->setTitle($title);
        $movie->setRating($rating);
        $movie->setYear($year);
        $movie->setPlot($plot);
        $movie->setGenre($genre);
        $movie->setActors($actors);
        $movie->setHealth(10);
        if ($rating == "N/A" || $rating < 5)
            $movie->setPower(1);
        else if ($rating >= 5 && $rating <= 7)
            $movie->setPower(10);
        else
            $movie->setPower(100);
        try{
            $entityManager->persist($movie);
            $entityManager->flush();
            }
        catch(\Exception $e){
            echo $e->getMessage();
        }
    }
    /**
     * @Route("/options/")
     */
    public function indexAction()
    {
        return $this->render('GameBundle:Default:index.html.twig', [
            "message" => ""
        ]);
    }

     /**
     * @Route("/game/")
     */
    public function getNewGame(Request $request)
    {
        $i = 0;
        $entityManager = $this->getDoctrine()->getManager();
        $id_movies = ["tt0106062", "tt0110357", "tt6723592", "tt2724064","tt1098327", "tt0368226", "tt0105643", "tt0800369", "tt1228705", "tt1477834"];
        $client = HttpClient::create();
        $qb = $entityManager->createQueryBuilder();
        $qb
        ->delete()
        ->from('GameBundle:Moviemon', 'a')
        ->getQuery()
        ->execute();
        $qb = $entityManager->createQueryBuilder();
        $qb
        ->delete()
        ->from('GameBundle:User', 'u')
        ->getQuery()
        ->execute();
        while($i < 10){
            $response = $client->request(
                    'GET',
                    'https://www.omdbapi.com/?i=' . $id_movies[$i] . '&apikey=' . $this::apikey
                )->toArray();
            $this->newMovie($response["Title"],$response["imdbRating"],
            $response["Year"],$response["Plot"], $response["Genre"], $response["Actors"]);
            $i++;
        }
        $form = $this->createFormBuilder()
        ->add('username', TypeTextType::class)
        ->add('Creer un nouveau joueur', SubmitType::class)
        ->getForm();
        $form->handleRequest($request);
        if($form->isValid() && $form->isSubmitted()){
            $message = $this->newUser($form["username"]->getData(), 10, 1);
            return $this->render('GameBundle::game.html.twig', [
                "message" => $message
            ]);
        }
        return $this->render('GameBundle::new.html.twig', [
            "form" => $form->createView()
        ]);
    }

     /**
     * @Route("/save/")
     */
    public function save(){
        $array_m = [];
        $entityManager = $this->getDoctrine()->getManager();
        $qb = $entityManager->createQueryBuilder();
        $movie = $qb
        ->select('u')
        ->from('GameBundle:Moviemon', 'u')
        ->getQuery()
        ->getResult();
        foreach($movie as $m){
            array_push($array_m, [
                'title' => $m->getTitle(),
                'rating' => $m->getRating(),
                'year' => $m->getYear(),
                'plot' => $m->getPlot(),
                'genre' => $m->getGenre(),
                'actors' => $m->getActors(),
            ]);
        }
        $array_u = [];
        $user = $qb
        ->select('a')
        ->from(User::class, 'a')
        ->getQuery()
        ->execute();
        foreach($user as $u){
            array_push($array_u, [
                'username' => $u->getUsername(),
                'health' => $u->getHealth(),
                'power' => $u->getPower(),
            ]);
            $name = $u->getUsername();
        }
        $array = [
            'movies' => $array_m,
            'user' => $array_u,
        ];
        $json = json_encode($array);
        $file = fopen(__DIR__ . '/' . $name . ".json", "c");
        fclose($file);
        file_put_contents(__DIR__ . '/' . $name . ".json", $json);
        return $this->render('GameBundle:Default:index.html.twig', [
            "message" => ""
        ]);
    }

    /**
     * @Route("/load/")
     */
    public function load(){

        $scandir = scandir(__DIR__);
        $files = [];
        foreach($scandir as $file){
            if (strpos($file, ".json") != FALSE)
                array_push($files, $file);
        }
        return $this->render('GameBundle::load.html.twig', [
            "message" => "",
            "files" => $files
        ]);
    }

      /**
     * @Route("/load/{name}")
     */
    public function loadingPlayer($name){
        $entityManager = $this->getDoctrine()->getManager();
        $qb = $entityManager->createQueryBuilder();
        $qb
        ->delete()
        ->from('GameBundle:Moviemon', 'a')
        ->getQuery()
        ->execute();
        $qb = $entityManager->createQueryBuilder();
        $qb
        ->delete()
        ->from('GameBundle:User', 'u')
        ->getQuery()
        ->execute();
        if ($name){
            $json = file_get_contents(__DIR__ . "/" . $name);
            $array = json_decode($json, true);
            $array_m = $array["movies"];
            $array_u = $array["user"];
            var_dump($array_u);
            $message = $this->newUser($array_u["username"], $array_u["health"], $array_u["power"]);
            foreach($array_m as $movie)
                $this->newMovie($movie["title"], $movie["rating"], $movie["year"], $movie["plot"],
                $movie["genre"],$movie["actors"]);
        }
        return $this->render('GameBundle::load.html.twig', [
            "message" => "",
            "files" => ""
        ]);
    }
}