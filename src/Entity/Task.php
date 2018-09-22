<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /** @ORM\Column(type="string", length=128) */
    private $title;

    /** @ORM\Column(type="text", nullable=true) */
    private $description;

    /** @ORM\Column(type="text", nullable=true) */
    private $followUp;

    /** @ORM\Column(type="boolean") */
    private $public;

    /** @ORM\Column(type="string", length=128) */
    private $priority;

    /** @ORM\Column(type="string", length=128) */
    private $status;

    /** @ORM\Column(type="simple_array", nullable=true) */
    private $tags;

    /** @ORM\Column(type="date", nullable=true) */
    private $targetCompleteDate;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $document;

    /** @ORM\ManyToMany(targetEntity="App\Entity\User") */
    private $userSubscribed;

    /** @ORM\ManyToOne(targetEntity="App\Entity\User") */
    private $userCreated;

    /** @ORM\ManyToOne(targetEntity="App\Entity\User") */
    private $userUpdated;

    /** @ORM\Column(type="datetime") */
    private $dateCreated;

    /** @ORM\Column(type="datetime") */
    private $dateUpdated;

    public function __construct()
    {
        $this->userSubscribed = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getTitle() ?? '';
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
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

    public function getFollowUp(): ?string
    {
        return $this->followUp;
    }

    public function setFollowUp(?string $followUp): self
    {
        $this->followUp = $followUp;
        return $this;
    }

    /** @return Collection|User[] */
    public function getUserSubscribed(): Collection
    {
        return $this->userSubscribed;
    }

    public function setUserSubscribed(array $users): self
    {
        $this->userSubscribed = $users;
        return $this;
    }

    public function getPublic(): bool
    {
        return $this->public;
    }

    public function getPublicString(): string
    {
        return $this->public ? 'Yes' : 'No';
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;
        return $this;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getTagsString(): ?string
    {
        return empty($this->tags) ? null : implode(', ', $this->tags);
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function getTargetCompleteDate(): ?\DateTime
    {
        return $this->targetCompleteDate;
    }

    public function getTargetCompleteDateString(): ?string
    {
        return $this->getTargetCompleteDate() === null ? null : $this->getTargetCompleteDate()->format('Y-m-d');
    }

    public function setTargetCompleteDate(?\DateTime $targetCompleteDate): self
    {
        $this->targetCompleteDate = $targetCompleteDate;
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
        return $this->getDateCreated()->format('Y-m-d @ h:i a');
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
        return $this->getDateUpdated()->format('Y-m-d @ h:i a');
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
