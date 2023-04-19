<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\Generic;

class Permissions extends Generic
{
    use ResponseTrait;

    protected $modelName = 'App\Models\PermissionModel';

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
        $data = $this->model->findAll();

        return $this->respond($data);
    }

}