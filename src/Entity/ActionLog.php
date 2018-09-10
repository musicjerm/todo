<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActionLogRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ActionLog
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
    private $action;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $detail;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="userCreated", nullable=true)
     */
    private $userCreated;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    public function getId()
    {
        return $this->id;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function getDetailShortString(): ?string
    {
        if (\strlen($this->getDetail()) > 43){
            return substr($this->getDetail(), 0, 40) . '...';
        }

        return $this->getDetail();
    }

    public function setDetail(?string $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    public function getUserCreated(): ?User
    {
        return $this->userCreated;
    }

    public function setUserCreated(?User $userCreated): self
    {
        $this->userCreated = $userCreated;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function getDateCreatedString(): string
    {
        return $this->getDateCreated() ? $this->getDateCreated()->format('Y-m-d @h:i a') : '';
    }

    /** @ORM\PrePersist() */
    public function setDateCreated(): self
    {
        $this->dateCreated = new \DateTime();

        return $this;
    }
}