<?php namespace App\Controllers;
 
 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;
 use App\Models\ContractorModel;
 use App\Models\VehicleModel;
 use App\Models\DriverVehicleAvailableModel;
  
 class Contractors extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\ContractorModel';

     public function _remap($method, ...$params)
     {
         $session = session();
 
         if (!$session->user) {
             $response = [
                 'status'   => 201,
                 'error'    => true,
                 'messages' => [
                     'success' => false,
                     'error'   => 'Debe autenticarse.',
                 ]
             ];
 
             return $this->respond($response);
         }
         
         return $this->{$method}(...$params);
     }
     
         // // create
    public function create()
    {

        $vehicleModel = new VehicleModel();
        $driverVehicleAvailableModel = new DriverVehicleAvailableModel();

        $vehicleData = array();
        $driverVehicleAvailableData = array();

        foreach ($this->model->allowedFields as $col) {
            $data[$col] = $this->request->getVar($col);
        }

        foreach ($vehicleModel->allowedFields as $col) {
            $vehicleData[$col] = $this->request->getVar($col);
        }

        if($vehicleData['contractor_id'] === '')
            $vehicleData['contractor_id'] = null;

        $vehicleId = $vehicleModel->insert($vehicleData);

        $errors = $this->model->errors();

        $ok = empty($errors);

        if(!$ok){
            $response = [
                'status'   => 201,
                'error'    => !$ok,
                'messages' => [
                    'success' => $ok ? 'Data Created' : false ,
                    'error'   => $errors,
                ]
            ];
            return $this->respondCreated($response);
        }

        $data['vehicle_id'] = $vehicleId;

        // $validatioRules = array();
        // foreach ($this->model->validationRules as $key => $value) {
        //     $validatioRules[$key] = str_replace('{'.$this->model->primaryKey.'}', $id, $value);
        // };

        // $this->model->setValidationRules($validatioRules);
        
        $driverId = $this->model->insert($data);

        $errors = $this->model->errors();

        $ok = empty($errors);

        if(!$ok){
            $response = [
                'status'   => 201,
                'error'    => !$ok,
                'messages' => [
                    'success' => $ok ? 'Data Created' : false ,
                    'error'   => $errors,
                ]
            ];

            $vehicleModel->delete($vehicleId);
            return $this->respondCreated($response);
        }
        
        if( $data['driver_available'] === 'Si' && $vehicleData['vehicle_status_id'] === '1'){

            $driverVehicleAvailableData['driver_id'] = $driverId;
            $driverVehicleAvailableData['vehicle_id'] = $vehicleId;

            $driverVehicleAvailableId = $driverVehicleAvailableModel->insert($driverVehicleAvailableData);
        }
        
        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Data Saved'
            ]
        ];
        return $this->respondCreated($response);
    }
 }