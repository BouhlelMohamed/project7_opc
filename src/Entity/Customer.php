<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 */
class Customer implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("customer:read")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="customer", cascade={"persist", "remove" })
     * @Groups("customer:read")
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("customer:read")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
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
