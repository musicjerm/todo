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
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var UploadedFile
     * @Assert\File(maxSize="50M")
     */
    public $document;
}