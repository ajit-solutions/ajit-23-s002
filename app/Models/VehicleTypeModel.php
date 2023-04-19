<?php namespace App\Models;
  
  use CodeIgniter\Model;
    
  class VehicleTypeModel extends Model
  {
      protected $table = 'vehicle_types';
      protected $primaryKey = 'vehicle_type_id';
      protected $allowedFields = [
        'vehicle_type_name'
      ];
      protected $returnType    = \App\Entities\VehicleType::class;

      public function getAll()
      {
        return $this
          ->asObject()
          ->orderBy('vehicle_type_name')
          ->findAll();
      }
  
  }