<?php namespace App\Controllers;
 
 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;
 use App\Models\OccupationModel;
  
 class Occupations extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\OccupationModel';
  
 }