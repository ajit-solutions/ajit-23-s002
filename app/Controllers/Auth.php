<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\Generic;
use App\Models\UserClientModel;
use App\Models\ClientModel;
use App\Models\UserModel;

// header("Access-Control-Allow-Origin: http://localhost:3000"); // cannot be a wildcard, you have to specify the name of the domain making the request here.
// header('Access-Control-Allow-Headers: Content-Type');
// header("Access-Control-Allow-Credentials: true"); // add this header
class Auth extends ResourceController
{
    use ResponseTrait;

    
    public function index()
    {
        $session = session();
        if($session->user){
            echo $session->user->user_name;
        }
    }

    public function login()
    {
        $session = session();

        $userModel = new UserModel();
        $user = $userModel->where('user_login', $this->request->getPost('user_login'))->first();

        $error = false;
        
        if($user){
            $verify_password  = password_verify($this->request->getPost('user_password'),$user->user_password);
            if($verify_password){
                $session->set('user', $user);
            }else{
                $session->destroy();
                $error = true;
            }
        }else{
            $session->destroy();
            $error = true;
        }

        unset($user->user_password);
        $response = [
            'status'   => 201,
            'error'    => $error,
            'messages' => [
                'success' => !$error ? 'Logged in user.' : 'error',
                'error'   => $error ? 'Usuario o contraseña incorrectos.' : '',
            ],
            'user' => !$error ? $user : null
        ];

        return $this->respond($response);
    }

    public function logout()
    {

        $session = session();
        $session->destroy();

        $response = [
            'status'   => 201,
            'error'    => false,
            'messages' => [
                'success' => 'Logged out user.',
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
