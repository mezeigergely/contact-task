<?php

namespace App\DTO;

class TicketFormDTO
{
    private $name;
    private $email;
    private $message;

    const TICKET_SUCCESS_MESSAGE = 'Köszönjük szépen a kérdésedet! Válaszunkkal hamarosan keresünk a megadott e-mail címen.';

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }
}
