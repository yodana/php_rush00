<?php

namespace GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Moviemon
 *
 * @ORM\Table(name="moviemon")
 * @ORM\Entity(repositoryClass="GameBundle\Repository\MoviemonRepository")
 */
class Moviemon
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(name="rating", type="integer")
     */
    private $rating;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="plot", type="text")
     */
    private $plot;

    /**
     * @var string
     *
     * @ORM\Column(name="genre", type="string", length=255)
     */
    private $genre;

    /**
     * @var string
     *
     * @ORM\Column(name="actors", type="text")
     */
    private $actors;


     /**
     * @var int
     *
     * @ORM\Column(name="health", type="integer")
     */
    private $health;

     /**
     * @var int
     *
     * @ORM\Column(name="power", type="integer")
     */
    private $power;
   
    /**
     * @var boolean
     *
     * @ORM\Column(name="captured", type="boolean")
     */
    private $captured;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

     /**
     * Get captured
     *
     * @return boolean
     */
    public function getCaptured()
    {
        return $this->captured;
    }

    /**
     * Set captured
     *
     * @param boolean $captured
     *
     * @return Moviemon
     */
    public function setCaptured($captured)
    {
        $this->captured = $captured;
        return $this->captured;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Moviemon
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
    
    /**
     * Get power
     *
     * @return int
     */
    public function getPower()
    {
        return $this->power;
    }
    
    /**
     * Get health
     *
     * @return int
     */
    public function getHealth()
    {
        return $this->health;
    }
    
    /**
     * Set power
     *
     * @param integer $power
     *
     * @return Moviemon
     */
    public function setPower($power)
    {
        $this->power = $power;

        return $this;
    }

    /**
     * Set health
     *
     * @param integer $health
     *
     * @return Moviemon
     */
    public function setHealth($health)
    {
        $this->health = $health;

        return $this;
    }


    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return Moviemon
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set year
     *
     * @param integer $year
     *
     * @return Moviemon
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set plot
     *
     * @param string $plot
     *
     * @return Moviemon
     */
    public function setPlot($plot)
    {
        $this->plot = $plot;

        return $this;
    }

    /**
     * Get plot
     *
     * @return string
     */
    public function getPlot()
    {
        return $this->plot;
    }

    /**
     * Set genre
     *
     * @param string $genre
     *
     * @return Moviemon
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return string
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set actors
     *
     * @param string $actors
     *
     * @return Moviemon
     */
    public function setActors($actors)
    {
        $this->actors = $actors;

        return $this;
    }

    /**
     * Get actors
     *
     * @return string
     */
    public function getActors()
    {
        return $this->actors;
    }
}

