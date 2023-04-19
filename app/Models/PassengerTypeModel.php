<?php

namespace App\Models;

use CodeIgniter\Model;

use Config\App;

class PassengerTypeModel extends Model
{
  protected $table = 'passenger_types';
  protected $primaryKey = 'passenger_type_id';
  protected $returnType = \App\Entities\PassengerType::class;
  protected $allowedFields = [    
    'passenger_type_name'
  ];

  public function getAll()
  {
    return $this
      ->asObject()
      ->orderBy('passenger_type_name')
      ->findAll();
  }

}
