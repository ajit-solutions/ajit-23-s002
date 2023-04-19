<?php namespace App\Models;
  
  use CodeIgniter\Model;
    
  class VehicleStatusModel extends Model
  {
      protected $table = 'vehicle_statuses';
      protected $primaryKey = 'vehicle_status_id';
      protected $allowedFields = [
        'vehicle_status_name',
        'vehicle_status_code'
      ];
      protected $returnType    = \App\Entities\VehicleStatus::class;

      public function getAll()
      {
        return $this
          ->asObject()
          ->orderBy('vehicle_status_name')
          ->findAll();
      }
  
  }