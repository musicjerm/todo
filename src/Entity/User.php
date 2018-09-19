<?php

namespace App\Entity;

use App\Form\User\UserData;
use App\Form\User\UserUpdateData;
use Doctrine\ORM\Mapping as ORM;
use Musicjerm\Bundle\JermBundle\Entity\User as BaseUser;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=128)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $password;

    /**
     * @ORM\Column(type="simple_array")
     */
    protected $roles;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $lastName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateUpdated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_created")
     */
    private $userCreated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_updated")
     */
    private $userUpdated;

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getDateCreated(): \DateTimeInterface
    {
        return $this->dateCreated;
    }

    /**
     * @ORM\PrePersist()
     * @return User
     */
    public function setDateCreated(): self
    {
        $this->dateCreated = new \DateTime();

        return $this;
    }

    public function getDateUpdated(): \DateTimeInterface
    {
        return $this->dateUpdated;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @return User
     */
    public function setDateUpdated(): self
    {
        $this->dateUpdated = new \DateTime();

        return $this;
    }

    public function getUserCreated(): ?self
    {
        return $this->userCreated;
    }

    public function setUserCreated(?self $userCreated): self
    {
        $this->userCreated = $userCreated;

        return $this;
    }

    public function getUserUpdated(): ?self
    {
        return $this->userUpdated;
    }

    public function setUserUpdated(?self $userUpdated): self
    {
        $this->userUpdated = $userUpdated;

        return $this;
    }

    public function getDateCreatedString(): string
    {
        return $this->getDateCreated()->format('Y-m-d @ h:i:s a');
    }

    public function getDateUpdatedString(): string
    {
        return $this->getDateUpdated()->format('Y-m-d @ h:i:s a');
    }

    /** @param UserData|UserUpdateData $dto */
    public function setDataFromDTO($dto): void
    {
        $this->username = $dto->username;
        $this->email = $dto->email;
        $this->firstName = $dto->firstName;
        $this->lastName = $dto->lastName;
        $dto->password === null ?: $this->password = password_hash($dto->password, PASSWORD_BCRYPT);
        $this->roles = [$dto->roles];
        $this->isActive = $dto->isActive;
    }
}