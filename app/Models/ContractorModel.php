<?php namespace App\Models;
  
  use CodeIgniter\Model;
    
  class ContractorModel extends Model
  {
      protected $table = 'contractors';
      protected $primaryKey = 'contractor_id
      ';
      protected $allowedFields = [
        'contractor_name',
        'contractor_surname',
        'contractor_id_card',
        'contractor_photo',
        'contractor_driving_license_photo',
        'contractor_driving_license_expiration_date',
        'contractor_available',
        'vehicle_id',
        
      ];
      protected $returnType    = \App\Entities\Contractor::class;

      public function getAll()
      {
        $clientModel = new ClientModel();
    
        $items = $this
          ->asObject()
          ->orderBy('contractor_name','contractor_surname')
          ->findAll();
    
        foreach ($items as &$item) {
          $client = $clientModel->find($item->client_id);
          $item->client_name = $client->client_name;
        }
        return $items;
      }
  
  }
  