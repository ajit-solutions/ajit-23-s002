<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\ClientModel;

use Config\App;

class TrackingModel extends Model
{
  protected $table = 'v_actives_trips';
  protected $primaryKey = 'vehicle_id';
  protected $returnType = \App\Entities\Office::class;
  protected $allowedFields = [
	'driver_id',
	'driver_name',
	'driver_surname',
	'driver_photo',
	'vehicle_registration_number',
	'brand_name',
	'trip_final_datetime',
	'trip_start_datetime',
  ];


  public function getAll()
  {
      $query = $this->db->query('SELECT * FROM v_actives_trips');

      return $query->getResult();
  }
}