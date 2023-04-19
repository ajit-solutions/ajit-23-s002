<?php namespace App\Models;
  
  use CodeIgniter\Model;
    
  class DriverVehicleAvailableModel extends Model
  {
      protected $table = 'drivers_vehicles_available';
      protected $primaryKey = 'driver_vehicle_avalable_id';
      protected $allowedFields = [
        'driver_id', 
        'vehicle_id'
      ];
      protected $returnType    = \App\Entities\DriverVehicleAvailable::class;

      protected $validationRules =  [
        'vehicle_id' => 'required',
        'driver_id' => 'required',
      ];
    
      protected $validationMessages = [
        'vehicle_id' => [
          'required' => 'El Vehículo es requerido.',
        ],
        'driver_id' => [
          'required' => 'El Chofer es requerido.',
        ]
      ];
   
      public function getAll()
      {
        return $this
          ->asObject()
          ->orderBy('driver_vehicle_avalable_id')
          ->findAll();
      }
  
  }