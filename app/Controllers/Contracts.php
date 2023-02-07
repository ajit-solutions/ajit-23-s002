<?php namespace App\Controllers;
 
 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;
 use App\Models\ContractModel;
 use App\Models\ExtendedContractModel;

 class Contracts extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\ContractModel';
     protected $extendedModelName = 'App\Models\ExtendedContractModel';
  
 }