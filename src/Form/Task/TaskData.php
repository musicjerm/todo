<?php

namespace App\Form\Task;

use App\Entity\User;
use App\Entity\UserGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class TaskData
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max="128")
     */
    public $title;

    /** @var string */
    public $description;

    /** @var string */
    public $followUp;

    /** @var ArrayCollection|UserGroup[] */
    public $userGroups;

    /** @var ArrayCollection|User[] */
    public $subbedUsers;

    /** @var boolean */
    public $public;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $priority;

    /**
     * @var string
     */
    public $status;

    /** @var \DateTime */
    public $targetCompleteDate;

    /**
     * @var string
     *@Assert\Length(max="128")
     */
    public $tags;

    /**
     * @var UploadedFile
     * @Assert\File(maxSize="50M")
     */
    public $document;
}