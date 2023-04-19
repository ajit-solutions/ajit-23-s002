<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\Generic;
use App\Models\UserClientModel;
use App\Models\ClientModel;


class Users extends Generic
{
    use ResponseTrait;

    protected $modelName = 'App\Models\UserModel';

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
        $userRoleModel = new \App\Models\UserRoleModel();
        $roleModel = new \App\Models\RoleModel();

        $data = $this->model
            ->asObject()
            ->orderBy('user_name')
            ->findAll();

        $roles = [];

        foreach ($data as &$item) {

            $userRoles = $userRoleModel->asObject()
                ->where("user_id", $item->user_id)
                ->findAll();

            unset($item->user_password);
            
            $item->roles = array();
            foreach ($userRoles as &$userRole) {
                $roles = $roleModel
                    ->asObject()
                    ->find($userRole->role_id);

                array_push($item->roles, $roles);
            }
        }

        return $this->respond($data);
    }

    public function create()
    {

        foreach ($this->model->allowedFields as $col) {
            $data[$col] = $this->request->getVar($col);
        }

        $data['user_password'] = password_hash($data['user_password'], PASSWORD_DEFAULT);

        $user_id = $this->model->insert($data);

        $errors = $this->model->errors();

        $ok = empty($errors);

        $response = [
            'status'   => 201,
            'error'    => !$ok,
            'messages' => [
                'success' => $ok ? 'Data Created' : false,
                'error'   => $errors,
            ]
        ];
        
        if (!$ok)
            return $this->respondCreated($response);

        $userRoleModel = new \App\Models\UserRoleModel();

        $roles = $this->request->getVar('roles');

        if (!empty($roles))
            foreach ($roles as $index => $role_id) {
                $rel_data = array(
                    'user_id' => $user_id,
                    'role_id' => $role_id
                );
                $userRoleModel->insert($rel_data);
            }

        $response = [
            'status'   => 201,
            'error'    => false,
            'messages' => [
                'success' => 'Data Created',
                'error'   => [],
            ]
        ];

        return $this->respondCreated($response);
    }

    public function update($id = null)
    {

        foreach ($this->model->allowedFields as $col) {
            $data[$col] = $this->request->getVar($col);
        }
        
        $data['user_password'] = password_hash($data['user_password'], PASSWORD_DEFAULT);

        $changePassword = $this->request->getVar('change_password');

        $validationRules = $this->model->validationRules;
        if(!$changePassword){
            unset($data['user_password'], $validationRules['user_password']);
        }

        // $validationRules = array();
        foreach ($validationRules as $key => $value) {
            $validationRules[$key] = str_replace('{'.$this->model->primaryKey.'}', $id, $value);
        };

        $this->model->setValidationRules($validationRules);

        $this->model->update($id, $data);

        $errors = $this->model->errors();

        $ok = empty($errors);

        $response = [
            'status'   => 201,
            'error'    => !$ok,
            'messages' => [
                'success' => $ok ? 'Datos actualizados.' : false,
                'error'   => $errors,
            ]
        ];

        if (!$ok)
            return $this->respondCreated($response);

        $rel_model = new \App\Models\UserRoleModel();

        $roles = $this->request->getVar('roles');

        $rel_model->where('user_id', $id)->delete();

        if (!empty($roles))
            foreach ($roles as $index => $role_id) {
                $rel_data = array(
                    'user_id' => $id,
                    'role_id' => $role_id
                );
                $rel_model->insert($rel_data);
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
}
