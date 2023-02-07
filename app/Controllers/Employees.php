<?php namespace App\Controllers;
 
 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;
 use App\Models\EmployeeModel;
 use App\Models\ExtendedEmployeeModel;

 class Employees extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\EmployeeModel';
     protected $extendedModelName = 'App\Models\ExtendedEmployeeModel';
  
 }