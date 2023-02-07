<?php

namespace App\Models;

use CodeIgniter\Model;

class DriverModel extends Model
{
  protected $table = 'drivers';
  protected $primaryKey = 'driver_id';
  protected $allowedFields = [
    'driver_name',
    'driver_surname',
    'driver_id_card',
    'driver_photo',
    'driver_driving_license_photo',
    'driver_driving_license_expiration_date',
    'driver_available'
  ];
  protected $returnType    = \App\Entities\Driver::class;

  public function getAll()
  {
    return $this
      ->asObject()
      ->orderBy('driver_name', 'driver_surname')
      ->findAll();
  }
}
