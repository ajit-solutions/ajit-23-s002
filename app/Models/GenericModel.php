<?php namespace App\Models;
  
  use CodeIgniter\Model;
    
  class GenericModel extends Model
  {
      protected $table = 'clients';
      protected $primaryKey = 'client_id';
      protected $allowedFields = [
        'client_name', 
        'client_address', 
        'client_location'
      ];
      protected $returnType    = \App\Entities\Client::class;
  
  }