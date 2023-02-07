<?php namespace App\Models;
  
  use CodeIgniter\Model;
    
  class ZoneModel extends Model
  {
      protected $table = 'exclusion_zones';
      protected $primaryKey = 'zone_id';
      protected $allowedFields = ['zone_points', 'zone_description'];
  }