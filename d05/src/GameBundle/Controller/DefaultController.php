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
    public $movie = [];
    
    public function verif(){
        $entityManager = $this->getDoctrine()->getManager();
        $m = $entityManager->getRepository(Moviemon::class)->findAll();
        $c = 0;
        foreach($m as $movie){
            if ($movie->getCaptured() == 1){
                $c += 1;
            }
        }
        return $c;
    }

    public function getMovies(){
        $entityManager = $this->getDoctrine()->getManager();
        $qb = $entityManager->createQueryBuilder();
        $movie = $qb
        ->select('u')
        ->from('GameBundle:Moviemon', 'u')
        ->getQuery()
        ->getResult();
        $array_m = [];
        foreach($movie as $m){
            array_push($array_m, [
                'title' => $m->getTitle(),
                'rating' => $m->getRating(),
                'year' => $m->getYear(),
                'plot' => $m->getPlot(),
                'genre' => $m->getGenre(),
                'actors' => $m->getActors(),
                'captured' => $m->getCaptured() == true ? "&#10004;": "&#10060;",
            ]);
        }
        return $array_m;
    }

    public function getMap($u_x, $u_y){
        $map = [];
        $x = 0;
        $y = 0;
        while($x <= 4){
            while($y <= 4){
                if ($y == $u_y && $x == $u_x)
                    $map[$x][$y] = '<img src="/eddy.png" width="100px" height="100px">';
                else
                    $map[$x][$y] = "";
                $y++;
            }
            $y = 0;
            $x++;
        }
        return $map;
    }

    public function newUser($name, $health, $power, $x, $y){
        $user = new User();
        $entityManager = $this->getDoctrine()->getManager();
        $user->setUsername($name);
        $user->setPower($power);
        $user->setHealth($health);
        $user->setX($x);
        $user->setY($y);
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
    public function newMovie($title, $rating, $year, $plot, $genre, $actors, $captured){
        $entityManager = $this->getDoctrine()->getManager();
        $movie = new Moviemon();
        $movie->setTitle($title);
        $movie->setRating($rating);
        $movie->setYear($year);
        $movie->setPlot($plot);
        $movie->setGenre($genre);
        $movie->setActors($actors);
        $movie->setHealth(10);
        $movie->setCaptured($captured);
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
        $cancel = false;
        $entityManager = $this->getDoctrine()->getManager();
        $qb = $entityManager->createQueryBuilder();
        $result = $qb
        ->select('u')
        ->from('GameBundle:User', 'u')
        ->getQuery()
        ->execute();
        if ($result){
            $cancel = true;
        }
        return $this->render('GameBundle:Default:index.html.twig', [
            "message" => "",
            "cancel" => $cancel
        ]);
    }

     /**
     * @Route("/new/")
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
            $response["Year"],$response["Plot"], $response["Genre"], $response["Actors"], 0);
            $i++;
        }
        $form = $this->createFormBuilder()
        ->add('username', TypeTextType::class)
        ->add('Creer un nouveau joueur', SubmitType::class)
        ->getForm();
        $form->handleRequest($request);
        if($form->isValid() && $form->isSubmitted()){
            $message = $this->newUser($form["username"]->getData(), 10, 1, 2, 2);
            return $this->render('GameBundle::game.html.twig', [
                "message" => $message,
                "fight" => false,
                "map" => $this->getMap(2, 2),
                "movies" => $this->getMovies()
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
                'captured' => $m->getCaptured()
            ]);
        }
        $array_u = [];
        $user = $qb
        ->select('a')
        ->from(User::class, 'a')
        ->getQuery()
        ->execute();
        $name = "";
        foreach($user as $u){
            array_push($array_u, [
                'username' => $u->getUsername(),
                'health' => $u->getHealth(),
                'power' => $u->getPower(),
                'x' => $u->getX(),
                'y' => $u->getY()
            ]);
            $name = $u->getUsername();
        }
        if ($name != ""){
            $array = [
                'movies' => $array_m,
                'user' => $array_u,
            ];
            $json = json_encode($array);
            file_put_contents($name . ".json", $json);
            return $this->render('GameBundle:Default:index.html.twig', [
                "message" => "",
                "cancel" => false
            ]);
        }
        return $this->render('GameBundle:Default:index.html.twig', [
            "message" => "Sauvegarde impossible pas de joueur!",
            "cancel" => false
        ]);
    }

    /**
     * @Route("/load/")
     */
    public function load(){

        $scandir = scandir('.');
        $files = [];
        foreach($scandir as $file){
            if (strpos($file, ".json") != FALSE)
                array_push($files, $file);
        }
        return $this->render('GameBundle::load.html.twig', [
            "message" => "",
            "files" => $files,
            "map" => false
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
            $json = file_get_contents($name);
            $array = json_decode($json, true);
            $array_m = $array["movies"];
            $array_u = $array["user"];
            $message = $this->newUser($array_u[0]["username"], $array_u[0]["health"], $array_u[0]["power"], $array_u[0]["x"], $array_u[0]["y"]);
            foreach($array_m as $movie)
                $this->newMovie($movie["title"], $movie["rating"], $movie["year"], $movie["plot"],
                $movie["genre"],$movie["actors"], $movie["captured"]);
        }
        return $this->render('GameBundle::load.html.twig', [
            "message" => "User loaded",
            "files" => "",
            "map" => true

        ]);
    }
    
    /**
     * @Route("/game/{move}/")
     */
    public function game($move){
        if($this->verif() == 10){
            return $this->render('GameBundle::win.html.twig', [
                "message" => "Congrats you win."
            ]);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $qb = $entityManager->createQueryBuilder();
        $array_u = [];
        $user = $qb
        ->select('a')
        ->from(User::class, 'a')
        ->getQuery()
        ->execute();
        $user[0]->setHealth(10);
        $entityManager->persist($user[0]);
        $entityManager->flush();
        foreach($user as $u){
            array_push($array_u, [
                'username' => $u->getUsername(),
                'health' => $u->getHealth(),
                'power' => $u->getPower(),
            ]);
            $name = $u->getUsername();
            if ($move == "left"){
                $u->setY((5 + (($u->getY() - 1) % 5)) % 5);
            }
            else if ($move == "right"){
                $u->setY((5 + (($u->getY() + 1) % 5)) % 5);
            }
            else if ($move == "down"){
                $u->setX((5 + (($u->getX() + 1) % 5)) % 5);
            }
            else if ($move == "up"){
                $u->setX((5 + (($u->getX() - 1) % 5)) % 5);
            }
            try{
                if ($move != "nomove"){
                    $entityManager->persist($u);
                    $entityManager->flush();
                }
            }
            catch(\Exception $e){
                echo $e->getMessage();
            }
        }
        if (!$user){
            return $this->render('GameBundle:Default:index.html.twig', [
                "message" => "Pas de joueur",
                "cancel" => false
            ]);
        }
        return $this->render('GameBundle::game.html.twig', [
            "message" => "",
            "fight" => true,
            "map" => $this->getMap($u->getX(), $u->getY()),
            "movies" => $this->getMovies()
        ]);
    }
       
    /**
     * @Route("/fight/{event}/")
     */
    public function fight($event){
        $message = "";
        $session = $this->get('session');
        $entityManager = $this->getDoctrine()->getManager();
        $qb = $entityManager->createQueryBuilder();
        $moviemon = $qb->select('u')
        ->from(Moviemon::class, 'u')
        ->where('u.captured = 0')
        ->getQuery()
        ->execute();
        $i = 0;
        foreach($moviemon as $m){
            $i++;
        }
        $rand = rand(0, $i);
        if(!$moviemon){
            return $this->render('GameBundle:Default:index.html.twig', [
                "message" => "Pas de joueur!",
                "cancel" => false
            ]);
        }
        $qb = $entityManager->createQueryBuilder();
        $users = $qb
        ->select('a')
        ->from(User::class, 'a')
        ->getQuery()
        ->execute();
        $i = 0;
        if ($event == "new"){
            foreach($moviemon as $m){
                if ($i == $rand){
                    $session->set('movie', [
                    'id' => $m->getId(),
                    'title' => $m->getTitle(),
                    'health' => $m->getHealth(),
                    'power' => $m->getPower(),
                    ]);
                }
                $i++;
            }
        }
        $movie = $session->get('movie');
        foreach($users as $user)
            $array_u = ['username' => $user->getUsername(),
            'health' => $user->getHealth(),
            'power' => $user->getPower(),
            ];
        if ($event == "random"){
            $rand = rand(1, 100);
            $mod = $movie["power"] / $user->getPower();
            if($mod < 1)
                $mod = 1;
            if (($rand % $mod) == 0){
                $message = "USER ATTACK AND TAKE ONE HEALTH TO THE MOVIE";
                $session->set('movie', [
                    'id' => $session->get('movie')["id"],
                    'title' => $session->get('movie')["title"],
                    'health' => $session->get('movie')["health"] - 1,
                    'power' => $session->get('movie')["power"],
                    ]);
                if($session->get('movie')["health"] <= 0){
                    if($this->verif() == 10){
                        return $this->render('GameBundle::win.html.twig', [
                            "message" => "Congrats you win."
                        ]);
                    }
                    $m = $entityManager->getRepository(Moviemon::class)->find($session->get('movie')["id"]);
                    $m->setHealth($session->get('movie')["health"]);
                    $m->setCaptured(true);
                    $user->setPower($user->getPower() + (5 * $session->get('movie')["power"]));
                    $entityManager->persist($m);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $message = "CONGRATS YOU CAPTURED THIS MOVIE";
                    return $this->render('GameBundle::game.html.twig', [
                        "message" => $message,
                        "fight" => false,
                        "map" => $this->getMap($user->getX(), $user->getY()),
                        "movies" => $this->getMovies()
                    ]);
                }
            }
            else{
                $array_u["health"] -= 1;
                $message = "YOU MISS! MONSTER ATTACK YOU! YOU LOSE 1 HP!";
                if ($user->getHealth() - 1 <= 0){
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
                    return $this->render('GameBundle:Default:index.html.twig', [
                        "message" => "YOU LOSE YOU USER HAS BEEN DELETED! Try again.",
                        "cancel" => false
                    ]);
                }
                $user->setHealth($user->getHealth() - 1);
                $entityManager->persist($user);
                $entityManager->flush();
            }
        }
        $poster = '<img src="/posters/' . str_replace(" ", "", $movie["title"]) . '.png" width="400px" height="600px">';
        $avatar = '<img src="/eddy.png" width="400px" height="400px">';
        return $this->render('GameBundle::fight.html.twig', [
            "poster" => $poster,
            "movie" => $session->get('movie'),
            "avatar" => $avatar,
            "user" => $array_u,
            "message" => $message
        ]);
    }

    /**
     * @Route("/details/")
     */
    public function details(){

        $movies = $this->getMovies();
        if(!$movies){
            return $this->render('GameBundle:Default:index.html.twig', [
                "message" => "Pas de joueur!",
                "cancel" => false
            ]);
        }
        return $this->render('GameBundle::details.html.twig', [
            "movies" => $movies
        ]);
    }
}