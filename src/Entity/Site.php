<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



/**
 * @ORM\Entity(repositoryClass="App\Repository\SiteRepository")
 * @UniqueEntity("name")
 */
class Site
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name;

    /**
     * @var Event[]
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="site")
     */
    private $events;

    /**
     * @var User[]
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="site")
     */
    private $users;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Event[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @param Event[] $events
     */
    public function setEvents(array $events): void
    {
        $this->events = $events;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @param User[] $users
     */
    public function setUsers($users): void
    {
        $this->users = $users;
    }
}
