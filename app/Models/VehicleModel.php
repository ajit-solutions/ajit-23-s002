<?php

namespace App\Models;

use CodeIgniter\Model;

use \App\Models\BrandModel;
use \App\Models\VehicleTypeModel;
use \App\Models\VehicleStatusModel;
use \App\Models\DriverModel;

class VehicleModel extends Model
{
  protected $table = 'vehicles';
  protected $primaryKey = 'vehicle_id';
  protected $allowedFields = [
    'vehicle_capacities',
    'vehicle_insurance_policy_number',
    'vehicle_registration_number',
    'vehicle_insurance_company',
    'vehicle_insurance_expiration_date',
    'vehicle_insurance_beneficiary',
    'brand_id',
    'vehicle_type_id',
    'vehicle_status_id',
    'contractor_id',
    
  ];

  protected $returnType    = \App\Entities\Vehicle::class;

  protected $validationRules =  [
    'vehicle_capacities' => 'required|greater_than[0]',
    'vehicle_insurance_policy_number' => 'required',
    'vehicle_registration_number' => 'required',
    'vehicle_insurance_company' => 'required',
    'vehicle_insurance_expiration_date' => 'required|valid_date',
    'brand_id' => 'required',
    'vehicle_type_id' => 'required',
    'vehicle_status_id' => 'required',
  ];

  protected $validationMessages = [
    'vehicle_capacities' => [
      'required' => 'El campo "Capacidad del vehículo" es obligatorio.',
      'greater_than' => 'El campo "Capacidad del vehículo" debe ser mayor que cero.',
    ],
    'vehicle_insurance_policy_number' => [
      'required' => 'El campo "Número de póliza de seguro" es obligatorio.',
    ],
    'vehicle_registration_number' => [
      'required' => 'El campo "Número de registro del vehículo" es obligatorio.',
    ],
    'vehicle_insurance_company' => [
      'required' => 'El campo "Compañía de seguros" es obligatorio.',
    ],
    'vehicle_insurance_expiration_date' => [
      'required' => 'El campo "Fecha de vencimiento del seguro" es obligatorio.',
      'valid_date' => 'El campo "Fecha de vencimiento del seguro" debe ser una fecha válida.',
    ],
    'brand_id' => [
      'required' => 'El campo "Marca del vehículo" es obligatorio.',
    ],
    'vehicle_type_id' => [
      'required' => 'El campo "Tipo de vehículo" es obligatorio.',
    ],
    'vehicle_status_id' => [
      'required' => 'El campo "Estado del vehículo" es obligatorio.',
    ],
  ];

  public function getAll()
  {
    $brandModel = new BrandModel();
    $vehicleTypeModel = new VehicleTypeModel();
    $vehicleStatusModel = new VehicleStatusModel();
    $driverModel = new DriverModel();

    $items = $this
      ->asObject()
      ->where('contractor_id')
      ->orderBy('vehicle_id')
      ->findAll();

    foreach ($items as &$item) {
      $brand = $brandModel->find($item->brand_id);
      $vehicleType = $vehicleTypeModel->find($item->vehicle_type_id);
      $vehicleStatus = $vehicleStatusModel->find($item->vehicle_status_id);
      // $driver = $driverModel->find(-1 | $item->driver_id);
      $item->brand_name = $brand->brand_name;
      $item->vehicle_type_name = isset($vehicleType->vehicle_type_name) ? $vehicleType->vehicle_type_name : '';
      $item->vehicle_status_name = $vehicleStatus->vehicle_status_name;
    }
    return $items;
  }

  public function get($id)
  {
    $brandModel = new BrandModel();
    $vehicleTypeModel = new VehicleTypeModel();
    $vehicleStatusModel = new VehicleStatusModel();
    $driverModel = new DriverModel();

    $item = $this->find($id);
    // if($id === '69')
    // var_dump($item);


    if (!empty($item)) {
      $brand = $brandModel->find($item->brand_id);
      $vehicleType = $vehicleTypeModel->find($item->vehicle_type_id);
      $vehicleStatus = $vehicleStatusModel->find($item->vehicle_status_id);
      // $driver = $driverModel->find(-1 | $item->driver_id);

      $item->brand_name = $brand->brand_name;
      $item->vehicle_type_name = $vehicleType->vehicle_type_name;
      $item->vehicle_status_name = $vehicleStatus->vehicle_status_name;
      // $item->driver_name = $driver && $driver->driver_name;
    }

    return $item;
  }
}
