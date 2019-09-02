<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @UniqueEntity(fields={"pseudo"}, message="There is already an account with this pseudo")
 */
class User implements UserInterface
{
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $oldPassword;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @var boolean $admin
     * @ORM\Column(type="boolean")
     */
    private $administrator = false;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $actif;

    /**
     * @ORM\Column(type="string", length=30, unique=true)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $imageFilename;

    /**
     * @var Site
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="users", cascade={"persist", "remove"})
     */
    private $site;

    /**
     * @var Event[]
     * @ORM\ManyToMany(targetEntity="App\Entity\Event", mappedBy="subscribers_users", cascade={"persist", "remove"})
     */
    private $events;

    /**
     * @var Event[]
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="user", cascade={"persist", "remove"})
     */
    private $createdEvents;

    /**
     * User constructor.
     * @param Event $events
     */
    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->createdEvents = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * @param mixed $actif
     */
    public function setActif($actif): void
    {
        $this->actif = $actif;
    }

    /**
     * @return mixed
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @param mixed $pseudo
     */
    public function setPseudo($pseudo): void
    {
        $this->pseudo = $pseudo;
    }

    /**
     * @return mixed
     */
    public function getImageFilename()
    {
        return $this->imageFilename;
    }

    /**
     * @param mixed $imageFilename
     */
    public function setImageFilename($imageFilename)
    {
        $this->imageFilename = $imageFilename;
        return $this;
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
    public function setSite(Site $site): void
    {
        $this->site = $site;
    }

    /**
     * @return Event[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param Event $events
     */
    public function setEvents(Event $events): void
    {
        $this->events = $events;
    }

    /**
     * @return Event
     */
    public function getCreatedEvents(): Event
    {
        return $this->createdEvents;
    }

    /**
     * @param Event $createdEvents
     */
    public function setCreatedEvents(Event $createdEvents): void
    {
        $this->createdEvents = $createdEvents;
    }

    /**
     * @param Event $event
     */
    public function removeCreatedEvents(Event $event): void
    {
        $this->createdEvents->removeElement($event);
        $event->setUser(null);
    }

    /**
     * @param Event $events
     */
    public function addEvents(Event $event): void
    {
        $this->events->add($event);
    }

    /**
     * @param Event $event
     */
    public function removeEvents(Event $event): void
    {
        $this->events->removeElement($event);
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
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return bool
     */
    public function isAdministrator(): bool
    {
        return $this->administrator;
    }

    /**
     * @param bool $administrator
     */
    public function setAdministrator(bool $administrator): void
    {
        $this->administrator = $administrator;
    }

    /**
     * @see UserInterface
     */
    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }


    public function setOldPassword(string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

}
