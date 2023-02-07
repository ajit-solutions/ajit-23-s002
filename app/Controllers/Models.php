<?php namespace App\Controllers;
 
 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;
 use App\Models\ModelModel;
 use App\Models\ExtendedModelModel;

 class Models extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\ModelModel';
     protected $extendedModelName = 'App\Models\ExtendedModelModel';
  
 }