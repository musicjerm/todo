<?php

namespace App\Form\User;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordData
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=6,
     *     minMessage="Please use a password with at least 6 characters"
     * )
     */
    public $password;

    /**
     * @var integer
     * @Assert\GreaterThan(
     *     value=1,
     *     message="Password strength is too low"
     * )
     */
    public $strength;
}