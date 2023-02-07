<?php namespace App\Models;
  
  use CodeIgniter\Model;

  use \App\Models\BrandModel;
    
  class VehicleModel extends Model
  {
      protected $table = 'vehicles';
      protected $primaryKey = 'vehicle_id';
      protected $allowedFields = [        
        'vehicle_capacities',
        'vehicle_available',
        'vehicle_insurance_policy_number',
        'vehicle_insurance_company',
        'vehicle_insurance_expiration_date',
        'vehicle_insurance_beneficiary',
        'brand_id'
        
      ];

      protected $returnType    = \App\Entities\Vehicle::class;

      public function getAll()
      {
        $clientModel = new ClientModel();
        $brandModel = new BrandModel();
    
        $items = $this
          ->asObject()
          ->orderBy('vehicle_id',)
          ->findAll();
    
        foreach ($items as &$item) {
          $brand = $brandModel->find($item->client_id);
    
          $item->brand_name = $item->brand_name;
        }
        return $items;
      }
  }