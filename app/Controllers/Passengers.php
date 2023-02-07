<?php namespace App\Controllers;

 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;
 use App\Models\PassengerModel;
 use App\Models\ExtendedPassengerModel;

 class Passengers extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\PassengerModel';
     protected $extendedModelName = 'App\Models\ExtendedPassengerModel';
  
 }