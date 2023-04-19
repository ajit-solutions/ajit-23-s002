<?php namespace App\Controllers;
 
 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;
 use App\Models\VehicleModel;
 use App\Models\ExtendedVehicleModel;

 class Vehicles extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\VehicleModel';
  
 }