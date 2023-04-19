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

     public function _remap($method, ...$params)
     {
         $session = session();
         
         if (!$session->user) {
             $response = [
                 'status'   => 201,
                 'error'    => true,
                 'messages' => [
                     'success' => false,
                     'error'   => 'Debe autenticarse.',
                 ]
             ];
 
             return $this->respond($response);
         }
         
         return $this->{$method}(...$params);
     }
  
 }