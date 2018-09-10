<?php

namespace App\Form\DTO;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Musicjerm\Bundle\JermBundle\Validator\Constraints as AppAssert;

/**
 * @AppAssert\UniqueDTO(
 *     entityClass="App\Entity\User",
 *     fields="username",
 *     errorPath="username",
 *     message="Username not available."
 * )
 * @AppAssert\UniqueDTO(
 *     entityClass="App\Entity\User",
 *     fields="email",
 *     errorPath="email",
 *     message="E-mail already registered."
 * )
 */
class UserData
{
    /** @var integer */
    public $id;

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

    /** @var array */
    public $roles;

    /** @var boolean */
    public $isActive;

    public function __toString()
    {
        return $this->username;
    }
}