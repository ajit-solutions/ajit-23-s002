<?php namespace App\Controllers;
 
 use CodeIgniter\RESTful\ResourceController;
 use CodeIgniter\API\ResponseTrait;
 use App\Controllers\Generic;
 use App\Models\RoleModel;
  
 class Roles extends Generic
 {
     use ResponseTrait;

     protected $modelName = 'App\Models\RoleModel';

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

     public function index()
     {
         $data = $this->model->where('role_id <> 1')->findAll();
 
         return $this->respond($data);
     }

     public function create()
     {
 
        foreach ($this->model->allowedFields as $col) {
            $data[$col] = $this->request->getVar($col);
        }

        $permissions = $this->request->getVar('permissions');

        $role_id = $this->model->insert($data);

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
        
        if(!$ok)
            return $this->respond($response);

 
         if (!empty($permissions)){
            $rolePermissionModel = new \App\Models\RolePermissionModel();
             foreach ($permissions as $index => $permission_id) {
                 $data = array(
                     'role_id' => $role_id,
                     'permission_id' => $permission_id
                 );
                 $rolePermissionModel->insert($data);
             }
        }

         $response = [
             'status'   => 201,
             'error'    => false,
             'messages' => [
                 'success' => 'Datos actualizados.',
                 'error'   => [],
             ]
         ];
 
         return $this->respondCreated($response);
     }
 
     public function update($id=null)
     {
 
        foreach ($this->model->allowedFields as $col) {
            $data[$col] = $this->request->getVar($col);
        }

        $permissions = $this->request->getVar('permissions');

        $validatioRules = array();
        foreach ($this->model->validationRules as $key => $value) {
            $validatioRules[$key] = str_replace('{'.$this->model->primaryKey.'}', $id, $value);
        };

        $this->model->setValidationRules($validatioRules);

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
        
        if(!$ok)
            return $this->respond($response);
        
        $rolePermissionModel = new \App\Models\RolePermissionModel();

        $rolePermissionModel->where('role_id', $id)->delete();


         if (!empty($permissions)){
             foreach ($permissions as $index => $permission_id) {
                 $data = array(
                     'role_id' => $id,
                     'permission_id' => $permission_id
                 );
                 $rolePermissionModel->insert($data);
             }
        }

         $response = [
             'status'   => 201,
             'error'    => false,
             'messages' => [
                 'success' => 'Datos actualizados.',
                 'error'   => [],
             ]
         ];
 
         return $this->respondCreated($response);
     }
 
     public function delete($id = null)
     {
         $this->model->where('role_id', $id)->delete();
 
         $response = [
             'status'   => 201,
             'error'    => false,
             'messages' => [
                 'success' => 'Data Deleted',
                 'error'   => [],
             ]
         ];
 
         return $this->respondCreated($response);
     }
  
 }