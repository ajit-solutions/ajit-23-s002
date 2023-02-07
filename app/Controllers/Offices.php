<?php namespace App\Controllers;
 
 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;
 use App\Models\OfficeModel;
 use App\Models\ExtendedOfficeModel;
 use App\Libraries\OfficesLib;

 class Offices extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\OfficeModel';
     protected $libraryName = 'App\Libraries\OfficesLib';
     protected $extendedModelName = 'App\Models\ExtendedOfficeModel';
  
 }