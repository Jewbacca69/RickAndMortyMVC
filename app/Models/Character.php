<?php

namespace App\Models;

class Character
{
    private int $id;
    private string $name;
    private string $image;
    private string $species;
    private string $gender;
    private string $origin;

    public function __construct
    (
        int $id,
        string $name,
        string $image,
        string $species,
        string $gender,
        string $origin
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->species = $species;
        $this->gender = $gender;
        $this->origin = $origin;

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getOrigin(): string
    {
        return $this->origin;
    }

    public function getSpecies(): string
    {
        return $this->species;
    }
}