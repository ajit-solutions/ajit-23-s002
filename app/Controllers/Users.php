<?php namespace App\Controllers;
 
 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;
 use App\Models\UserModel;
 use App\Models\ExtendedUserModel;

 class Users extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\UserModel';
     protected $extendedModelName = 'App\Models\ExtendedUserModel';
  
 }