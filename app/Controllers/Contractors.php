<?php namespace App\Controllers;
 
 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;
 use App\Models\ContractorModel;
  
 class Contractors extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\ContractorModel';
  
 }