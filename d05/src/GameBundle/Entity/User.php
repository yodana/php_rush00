<?php

namespace GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="GameBundle\Repository\UserRepository")
 */
class User
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
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

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
     * @var int
     *
     * @ORM\Column(name="x", type="integer")
     */
    private $x;

     /**
     * @var int
     *
     * @ORM\Column(name="y", type="integer")
     */
    private $y;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set health.
     *
     * @param int $health
     *
     * @return User
     */
    public function setHealth($health)
    {
        $this->health = $health;

        return $this;
    }

    /**
     * Set position x.
     *
     * @param int $x
     *
     * @return User
     */
    public function setX($x)
    {
        $this->x = $x;

        return $this;
    }


    /**
     * Set position y.
     *
     * @param int $y
     *
     * @return User
     */
    public function setY($y)
    {
        $this->y = $y;

        return $this;
    }

      /**
     * Get x.
     *
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

      /**
     * Get y.
     *
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Get health.
     *
     * @return int
     */
    public function getHealth()
    {
        return $this->health;
    }

    /**
     * Set power.
     *
     * @param int $power
     *
     * @return User
     */
    public function setPower($power)
    {
        $this->power = $power;

        return $this;
    }

    /**
     * Get power.
     *
     * @return int
     */
    public function getPower()
    {
        return $this->power;
    }
}
