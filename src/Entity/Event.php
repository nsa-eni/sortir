<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_start;

    /**
     * @ORM\Column(type="integer")
     */
    private $time_delay_minutes;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_end_of_registration;

    /**
     * @ORM\Column(type="integer")
     */
    private $max_number_places;

    /**
     * @ORM\Column(type="text")
     */
    private $info;

    /**
     * @var State $state
     * @ORM\ManyToOne(targetEntity="App\Entity\State", inversedBy="events", cascade={"persist"})
     */
    private $state;

    /**
     * @var Location $state
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="events", cascade={"persist"})
     */
    private $location;

    /**
     * @var Site
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="events")
     */
    private $site;

    /**
     * @var User[]
     * @ORM\ManyToMany(targetEntity="App\Entity\User",  inversedBy="events", cascade={"persist", "remove"})
     */
    private $subscribers_users;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="createdEvents")
     */
    private $user;

    /**
     * Event constructor.
     */
    public function __construct()
    {
        $this->subscribers_users = new ArrayCollection();
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return User[]
     */
    public function getSubscribersUsers()
    {
        return $this->subscribers_users;
    }

    /**
     * @param User[] $subscribers_users
     */
    public function setSubscribersUsers(array $subscribers_users): void
    {
        $this->subscribers_users = $subscribers_users;
    }


    /**
     * @param mixed $subscribers_users
     */
    public function addSubscribersUsers(User $user): void
    {
        $this->subscribers_users->add($user);
    }

    /**
     * @param User $user
     */
    public function removeSubscribersUsers(User $user): void
    {
        $this->subscribers_users->removeElement($user);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDateStart()
    {
        return $this->date_start;
    }

    /**
     * @param mixed $date_start
     */
    public function setDateStart($date_start): void
    {
        $this->date_start = $date_start;
    }

    /**
     * @return mixed
     */
    public function getTimeDelayMinutes()
    {
        return $this->time_delay_minutes;
    }

    /**
     * @param mixed $time_delay_minutes
     */
    public function setTimeDelayMinutes($time_delay_minutes): void
    {
        $this->time_delay_minutes = $time_delay_minutes;
    }

    /**
     * @return mixed
     */
    public function getDateEndOfRegistration()
    {
        return $this->date_end_of_registration;
    }

    /**
     * @param mixed $date_end_of_registration
     */
    public function setDateEndOfRegistration($date_end_of_registration): void
    {
        $this->date_end_of_registration = $date_end_of_registration;
    }

    /**
     * @return mixed
     */
    public function getMaxNumberPlaces()
    {
        return $this->max_number_places;
    }

    /**
     * @param mixed $max_number_places
     */
    public function setMaxNumberPlaces($max_number_places): void
    {
        $this->max_number_places = $max_number_places;
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param mixed $info
     */
    public function setInfo($info): void
    {
        $this->info = $info;
    }

    /**
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param State $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Site $site
     */
    public function setSite($site): void
    {
        $this->site = $site;
    }

}
