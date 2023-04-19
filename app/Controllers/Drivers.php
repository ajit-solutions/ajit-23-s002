<?php namespace App\Controllers;
 
 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;
 use App\Models\DriverModel;
  
 class Drivers extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\DriverModel';

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

     public function create()
     {
         $license_upload_response = (object) $this->uploadImage('driver_driving_license_photo', 'drivers');
 
 
         foreach ($this->model->allowedFields as $col) {
             $value = !is_null($this->request->getVar($col)) ? trim($this->request->getVar($col)) : null;
             $data[$col] = empty($value) ? null : $value;
         }
 
         if (!$license_upload_response->error && $license_upload_response->file)
             $data['driver_driving_license_photo'] = !$license_upload_response->error ? $license_upload_response->file : null;
 
         $this->model->insert($data);
 
         $errors = $this->model->errors();
 
         $ok = empty($errors);
 
         /**
          * TODO: Delete uploaded image if errors 
          */
 
         $response = [
             'status'   => 201,
             'error'    => !$ok,
             'messages' => [
                 'success' => $ok ? 'Data Created' : false,
                 'error'   => $errors,
                 'upload'  => $license_upload_response->error
             ]
         ];
 
         return $this->respondCreated($response);
     }
 
     public function update($id = null)
     {
        $license_upload_response = (object) $this->uploadImage('driver_driving_license_photo', 'drivers');
        $photo_upload_response = (object) $this->uploadImage('driver_photo', 'drivers');
 
         foreach ($this->model->allowedFields as $col) {
            if(!in_array($col, array('driver_driving_license_photo', 'driver_photo')))
                $data[$col] = $this->request->getVar($col);
         }
 
         if (!$license_upload_response->error && $license_upload_response->file){
             $data['driver_driving_license_photo'] = !$license_upload_response->error ? $license_upload_response->file : null;
        }

         if (!$photo_upload_response->error && $photo_upload_response->file){
             $data['driver_photo'] = !$photo_upload_response->error ? $photo_upload_response->file : null;
        }

         $validatioRules = array();
         foreach ($this->model->validationRules as $key => $value) {
             $validatioRules[$key] = str_replace('{'.$this->model->primaryKey.'}', $id, $value);
         };
 
         $this->model->setValidationRules($validatioRules);
 
         $this->model->update($id, $data);
 
         $errors = $this->model->errors();
 
         $ok = empty($errors);
 
         /**
          * TODO: Delete uploaded image if errors 
          */
 
         $response = [
             'status'   => 201,
             'error'    => !$ok,
             'messages' => [
                 'success' => $ok ? 'Datos actualizados.' : false,
                 'error'   => $errors,
                 'upload'  => $license_upload_response->error
             ]
         ];
 
         return $this->respondCreated($response);
     }
 
     public function delete($id = null)
     {
         $response = [
             'status'   => 200,
             'error'    => null,
             'messages' => [
                 'success' => 'Data Deleted'
             ]
         ];
 
         $data = $this->model->find($id);
 
         if ($data) {
             $vehicleModel = new \App\Models\VehicleModel();
 
             $vehicle = $vehicleModel->where('brand_id', $id)->find();
 
             if(!empty($vehicle)){
                 $response = [
                     'status'   => 201,
                     'code'     => '',
                     'error'    => true,
                     'messages' => [
                         'error' => ['No se puede borrar, existen elementos vinculados a esta marca.'],
                         'success' => null
                     ]
                 ];
                 return $this->respondDeleted($response);
             }
 
             $this->model->delete($id);
 
 
             $error = $this->model->db->error();
 
             $response = [
                 'status'   => 201,
                 'code'     => $error['code'],
                 'error'    => !!$error['code'],
                 'messages' => [
                     'error' => $error['message'],
                     'success' => !$error['code'] ? 'Data Deleted' : null
                 ]
             ];
 
             // }
         } else {
             $response = [
                 'status'   => 200,
                 'error'    => true,
                 'messages' => [
                     'error' => 'No Data Found with id ' . $id
                 ]
             ];
         }
 
         return $this->respondDeleted($response);
     }
  
 }