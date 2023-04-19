<?php namespace App\Controllers;
 
 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;

 class Routes extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\RouteModel';

    //  public function _remap($method, ...$params)
    //  {
    //      $session = session();
         
    //      if (!$session->user) {
    //          $response = [
    //              'status'   => 201,
    //              'error'    => true,
    //              'messages' => [
    //                  'success' => false,
    //                  'error'   => 'Debe autenticarse.',
    //              ]
    //          ];
 
    //          return $this->respond($response);
    //      }
         
    //      return $this->{$method}(...$params);
    //  }
  
 }