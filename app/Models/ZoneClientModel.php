<?php

namespace App\Models;

use CodeIgniter\Model;

class ZoneClientModel extends Model
{
  protected $table = 'exclusion_zone_client';
  protected $primaryKey = 'exclusion_zone_client_id';
  protected $allowedFields = [
    'client_id',
    'exclusion_zone_id',
  ];

  protected $returnType    = \App\Entities\ZoneClient::class;


}
