<?php

namespace App\Form\DTO;

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

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $followUp;

    /**
     * @var UploadedFile
     * @Assert\File(maxSize="50M")
     */
    public $document;
}