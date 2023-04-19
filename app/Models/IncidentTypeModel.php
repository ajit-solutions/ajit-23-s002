<?php namespace App\Models;
  
  use CodeIgniter\Model;

  use \App\Models\BrandModel;
  use \App\Models\VehicleTypeModel;
  use \App\Models\VehicleStatusModel;
  use \App\Models\TripModel;
    
  class IncidentTypeModel extends Model
  {
      protected $table = 'incident_types';
      protected $primaryKey = 'incident_type_id';
      protected $allowedFields = [             
        'incident_type_name',
      ];

      protected $returnType    = \App\Entities\IncidentType::class;

      public function getAll()
      {
    
        return $this
          ->asObject()
          ->orderBy('incident_type_name')
          ->findAll();

      }
  }