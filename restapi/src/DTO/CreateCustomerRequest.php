<?php
// src/DTO/CreateCustomerRequest.php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateCustomerRequest
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    private string $name;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     */
    private string $email;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
