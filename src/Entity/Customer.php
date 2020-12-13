<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     message="I think you're already registered!"
 * )
 */
class Customer implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"getCustomer","show_one_user"})
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="customer", cascade={"persist", "remove" })
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The field must not be empty")
     * @Assert\Email()
     * @Groups({"getCustomer"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The field must not be empty")
     */
    private $password;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCustomer($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getCustomer() === $this) {
                $user->setCustomer(null);
            }
        }

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
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

    public function getRoles()
    {
        return [];
    }
    
    public function getUsername()
    {
        return $this->email;
    }
}
