<?php

namespace App\Models;

use CodeIgniter\Model;

class ZoneModel extends Model
{
  protected $table = 'exclusion_zones';
  protected $primaryKey = 'exclusion_zone_id';
  protected $allowedFields = [
    'exclusion_zone_name',
    'exclusion_zone_description',
    'exclusion_zone_polygons',
    'exclusion_zone_general',
  ];

  protected $returnType    = \App\Entities\Zone::class;

  // Validation
  protected $validationRules =  [
    'exclusion_zone_name' => 'required',
    'exclusion_zone_polygons' => 'required|valid_json',
  ];

  protected $validationMessages = [
    'exclusion_zone_name' => [
      'required' => 'Can not be empty.',
    ],
    'exclusion_zone_polygons' => [
      'required' => 'Can not be empty.',
      'valid_json' => 'Not valid json.',
    ]
  ];
}
