<?php namespace App\Controllers;
 
 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;
 use App\Models\TripModel;
 use App\Models\ExtendedTripModel;

 class Trips extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\TripModel';
     protected $extendedModelName = 'App\Models\ExtendedTripModel';
  
 }