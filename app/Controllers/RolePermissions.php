<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\Generic;

class RolePermissions extends Generic
{
    use ResponseTrait;

    protected $modelName = 'App\Models\RolePermissionModel';

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

        $role_id = $this->request->getVar('role_id');
        $permissions = $this->request->getVar('permissions');

        if (!empty($permissions))
            foreach ($permissions as $index => $permission_id) {
                $data = array(
                    'role_id' => $role_id,
                    'permission_id' => $permission_id
                );
                $this->model->insert($data);
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

    public function update($id = null)
    {

        $role_id = $this->request->getVar('role_id');
        $permissions = $this->request->getVar('permissions');

        $this->model->where('role_id', $role_id)->delete();

        if (!empty($permissions))
            foreach ($permissions as $index => $permission_id) {
                $data = array(
                    'role_id' => $role_id,
                    'permission_id' => $permission_id
                );
                $this->model->insert($data);
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