<?php namespace App\Controllers;
 
 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;

 class Offices extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\OfficeModel';

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

     public function update($id = null)
     {
         foreach ($this->model->allowedFields as $col) {
             $data[$col] = $this->request->getVar($col);
         }
 
         $this->model->update($id, $data);
 
         $errors = $this->model->errors();
 
         $ok = empty($errors);
 
         $response = [
             'status'   => 201,
             'error'    => !$ok,
             'messages' => [
                 'success' => $ok ? 'Datos actualizados.' : false ,
                 'error'   => $errors,
             ]
         ];
 
         return $this->respond($response);
     }
  
 }