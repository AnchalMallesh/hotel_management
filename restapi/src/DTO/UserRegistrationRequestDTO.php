<?php
// src/DTO/UserRegistrationRequestDTO.php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationRequestDTO
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(min=4, max=255)
     */
    public ?string $username;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     */
    public ?string $email;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=6)
     */
    public ?string $password;
}
