<?php namespace App\Controllers;
 
 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;
 use App\Models\ContractorModel;
 use App\Models\VehicleModel;
 use App\Models\DriverVehicleAvailableModel;
  
 class DriversVehiclesAvailable extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\DriverVehicleAvailableModel';

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

 }