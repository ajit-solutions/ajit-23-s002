<?php

namespace App\Models;

use CodeIgniter\Model;

use \App\Models\RouteModel;
use \App\Models\VehicleModel;
use \App\Models\VehicleTypeModel;
use \App\Models\BrandModel;
use \App\Models\OfficeModel;
use \App\Models\ClientModel;
use \App\Models\WorkShiftModel;
use \App\Models\PassengerModel;
use \App\Models\DriverModel;
use \App\Models\DriverVehicleAvailableModel;

class TripModel extends Model
{
  protected $table = 'trips';
  protected $primaryKey = 'trip_id';
  protected $allowedFields = [
    'route_id',
    'vehicle_id',
    'work_shift_id',
    // 'trip_scheduled_datetime',
    'trip_start_datetime',
    'trip_final_datetime',
    'trip_vehicle_km',
    'include_other_routes'
  ];

  protected $returnType    = \App\Entities\Trip::class;

  protected $validationRules =  [
    'route_id' => 'required|integer',
    'vehicle_id' => 'required|integer',
    'work_shift_id' => 'required|integer',
    'trip_vehicle_km' => 'integer',
    // 'trip_start_datetime' => 'required|valid_date|greater_than_equal_to[today]',
    // 'trip_final_datetime' => 'required|valid_date|greater_than[trip_start_datetime]',
  ];

  protected $validationMessages = [
    'route_id' => [
      'required' => 'El campo de la ruta es obligatorio.',
      'integer' => 'El campo de la ruta debe ser un número entero.'
    ],
    'vehicle_id' => [
      'required' => 'El campo del vehículo es obligatorio.',
      'integer' => 'El campo del vehículo debe ser un número entero.'
    ],
    'work_shift_id' => [
      'required' => 'El campo del turno de trabajo es obligatorio.',
      'integer' => 'El campo del turno de trabajo debe ser un número entero.'
    ],
    'trip_vehicle_km' => [
      'integer' => 'El campo de los kilómetros del vehículo debe ser un número.',
    ],
    // 'trip_start_datetime' => [
    //   'required' => 'El campo de la fecha y hora de inicio del viaje es obligatorio.',
    //   'valid_date' => 'El campo de la fecha y hora de inicio del viaje no es una fecha válida.',
    //   'greater_than_equal_to' => 'El campo de la fecha y hora de inicio del viaje no puede ser menor que hoy',
    // ],
    // 'trip_final_datetime' => [
    //   'required' => 'El campo de la fecha y hora de finalización del viaje es obligatorio.',
    //   'valid_date' => 'El campo de la fecha y hora de finalización del viaje no es una fecha válida.',
    //   'greater_than' => 'La fecha y hora de finalización del viaje debe ser mayor que la fecha y hora de inicio del viaje.',
    // ],
  ];

  public function getAll()
  {
    $routeModel = new RouteModel();
    $vehicleModel = new VehicleModel();
    $vehicleTypeModel = new VehicleTypeModel();
    $brandModel = new BrandModel();
    $officeModel = new OfficeModel();
    $clientModel = new ClientModel();
    $workShiftModel = new WorkShiftModel();
    $passengerModel = new PassengerModel();
    $driverVehicleAvailableModel = new DriverVehicleAvailableModel();
    $driverModel = new DriverModel();

    $items = $this
      ->asObject()
      ->orderBy('trip_start_datetime', 'Asc')
      ->findAll();

    foreach ($items as &$item) {
      $route = $routeModel->find($item->route_id);
      $vehicle = $vehicleModel->find($item->vehicle_id);
      $workShift = $workShiftModel->find($item->work_shift_id);

      $vehicleType = $vehicle ? $vehicleTypeModel->find($vehicle->vehicle_type_id) : (object)array();

      $brand = $vehicle ? $brandModel->find($vehicle->brand_id) : (object)array();

      $client = $clientModel->find($workShift->work_shift_id);
      $office = $officeModel->find($workShift->office_id);

      $driverVehicleArray = $driverVehicleAvailableModel
        ->asObject()
        ->where('vehicle_id', $item->vehicle_id)
        ->findAll();


      $driverVehicle = array_pop($driverVehicleArray);
      $driver = $driverVehicle ? $driverModel->find($driverVehicle->driver_id) : (object)array();

      $passengers = $passengerModel
        ->asObject()
        ->orderBy('passenger_name', 'passenger_surname')
        ->where('trip_passengers.trip_id', $item->trip_id)
        ->join('trip_passengers', 'trip_passengers.passenger_id = passengers.passenger_id', 'LEFT')
        ->findAll();

      $item->brand = $brand;
      $item->vehicle_type = $vehicleType;
      $item->vehicle = $vehicle;
      $item->driver = $driver;
      $item->route = $route;
      $item->client = $client;
      $item->office = $office;
      $item->workShift = $workShift;
      $item->passengers = $passengers;
    };

    return $items;
  }

  public function get($id)
  {
    $brandModel = new BrandModel();
    $vehicleTypeModel = new VehicleTypeModel();
    $vehicleStatusModel = new VehicleStatusModel();
    $employeeModel = new EmployeeModel();

    $items = $this
      ->asObject()
      ->where('vehicle_id', $id)
      ->orderBy('vehicle_id')
      ->findAll();

    foreach ($items as &$item) {
      $brand = $brandModel->find($item->brand_id);
      $vehicleType = $vehicleTypeModel->find($item->vehicle_type_id);
      $vehicleStatus = $vehicleStatusModel->find($item->vehicle_status_id);
      $employee = $employeeModel->find(-1 | $item->employee_id);

      $item->brand_name = $brand->brand_name;
      $item->vehicle_type_name = $vehicleType->vehicle_type_name;
      $item->vehicle_status_name = $vehicleStatus->vehicle_status_name;
      $item->employee_name = $employee->employee_name;
    }
    return $items;
  }
}
