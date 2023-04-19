<?php namespace App\Models;
  
  use CodeIgniter\Model;

  use \App\Models\BrandModel;
  use \App\Models\VehicleTypeModel;
  use \App\Models\VehicleStatusModel;
  use \App\Models\IncidentTypeModel;
  use \App\Models\TripModel;
    
  class IncidentModel extends Model
  {
      protected $table = 'incidents';
      protected $primaryKey = 'incident_id';
      protected $allowedFields = [             
        'trip_id',
        'incident_type_id',
        'incident_title',
        'incident_description',
        'incident_datetime',               
      ];

      protected $returnType    = \App\Entities\Incident::class;

      public function getAll()
      {
        $incidentTypeModel = new IncidentTypeModel();
        $tripModel = new TripModel();
    
        $items = $this
          ->asObject()
          ->orderBy('incident_datetime', 'Desc')
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