<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Office extends Entity
{
    public $client_name;

    public function __construct (array $data = null)
    {
        parent::__construct($data);

        $this->client_name = '';
    }
}