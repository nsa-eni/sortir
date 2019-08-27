<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 */
class City
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Le champ Ville ne peut pas être vide !")
     */
    private $name;

    /**
     * @var Location[]
     * @ORM\OneToMany(targetEntity="App\Entity\Location", mappedBy="city")
     */
    private $locations;

    /**
     * @ORM\Column(type="string", length=5)
     * @Assert\NotBlank(message="Le champ Code postal ne peut pas être vide !")
     * @Assert\Regex("/[0-9]{5}/",message="Code postal au format 75000!")
     */
    private $zipCode;

    /**
     * City constructor.
     */
    public function __construct()
    {
        $this->locations = new ArrayCollection();
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

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * @return Location[]
     */
    public function getLocations(): array
    {
        return $this->locations;
    }

    /**
     * @param Location[] $locations
     */
    public function setLocations(array $locations): void
    {
        $this->locations = $locations;
    }
}
