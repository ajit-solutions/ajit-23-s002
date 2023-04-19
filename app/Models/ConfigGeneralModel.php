<?php namespace App\Models;
  
  use CodeIgniter\Model;
    
  class ConfigGeneralModel extends Model
  {
      protected $table = 'config_general';
      protected $primaryKey = 'config_general_id';
      protected $allowedFields = [
        'training_passengers_expiration_days',
        'request_mileage_data_internal_vehicles',
        'request_mileage_data_contractor_vehicles',
        'daytime_start',
        'daytime_end',
        'night_time_start',
        'night_time_end',
        'default_travel_time',
        'driver_license_expiration_limit',        
      ];
      protected $returnType    = \App\Entities\ConfigGeneral::class;
  
  }