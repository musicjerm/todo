<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(type="string", length=255) */
    private $name;

    /** @ORM\Column(type="text", nullable=true) */
    private $description;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $document;

    /** @ORM\ManyToOne(targetEntity="App\Entity\User") */
    private $userCreated;

    /** @ORM\ManyToOne(targetEntity="App\Entity\User") */
    private $userUpdated;

    /** @ORM\Column(type="datetime") */
    private $dateCreated;

    /** @ORM\Column(type="datetime") */
    private $dateUpdated;

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
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

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(?string $document): self
    {
        $this->document = $document;
        return $this;
    }

    public function getUserCreated(): User
    {
        return $this->userCreated;
    }

    public function setUserCreated(User $userCreated): self
    {
        $this->userCreated = $userCreated;
        return $this;
    }

    public function getUserUpdated(): User
    {
        return $this->userUpdated;
    }

    public function setUserUpdated(User $userUpdated): self
    {
        $this->userUpdated = $userUpdated;
        return $this;
    }

    public function getDateCreated(): \DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function getDateCreatedString(): string
    {
        return $this->getDateCreated()->format('Y-m-d @ H:i:s');
    }

    /**
     * @ORM\PrePersist()
     * @return Task
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

    public function getDateUpdatedString(): string
    {
        return $this->getDateUpdated()->format('Y-m-d @ H:i:s');
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @return Task
     */
    public function setDateUpdated(): self
    {
        $this->dateUpdated = new \DateTime();
        return $this;
    }
}
