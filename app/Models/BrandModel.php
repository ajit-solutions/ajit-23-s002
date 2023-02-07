<?php namespace App\Models;
  
  use CodeIgniter\Model;
    
  class BrandModel extends Model
  {
      protected $table = 'brands';
      protected $primaryKey = 'brand_id';
      protected $allowedFields = [
        'brand_name', 
        'brand_logo'
      ];
      protected $returnType    = \App\Entities\Brand::class;

      public function getAll()
      {
        return $this
          ->asObject()
          ->orderBy('brand_name')
          ->findAll();
      }
  
  }