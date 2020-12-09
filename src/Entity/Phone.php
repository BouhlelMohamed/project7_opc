<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=PhoneRepository::class)
 * @UniqueEntity("name")
 * )
 */
class Phone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("phone:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The field must not be empty")
     * @Assert\Length(
     *     min = 3,
     *     max = 25
     * )
     * @Groups("phone:read")
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(message="The field must not be empty")
     * @Assert\Length(
     *     max = 1
     * )
     * @Assert\Regex(
     *     pattern="/^[0-9]+(\.[0-9]{1,2})?$/",
     *     match="true"
     * )
     * @Groups("phone:read")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("phone:read")
     */
    private $color;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(
     *     min = 3,
     *     max = 255
     * )
     * @Groups("phone:read")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("phone:read")
     */
    private $createdAt;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

}
