<?php

namespace App\Form\Setup;

use Symfony\Component\Validator\Constraints as Assert;

class CreateAdminData
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max="128")
     */
    public $username;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $password;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max="128")
     * @Assert\Email()
     */
    public $email;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max="32")
     */
    public $firstName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max="32")
     */
    public $lastName;
}