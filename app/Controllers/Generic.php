<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\IncomingRequest;
use Config\Services;

class Generic extends ResourceController
{

    use ResponseTrait;

    public  $validation;
    
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

    // get all
    public function index()
    {       
        $data = $this->model->getAll();

        return $this->respond($data);
    }

    // get single
    public function show($id = null)
    {
        $data = $this->model->find([$this->model->primaryKey => $id]);
        if (!empty($data)) {
            return $this->respond($data[0]);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    // // create
    public function create()
    {

        foreach ($this->model->allowedFields as $col) {
            $data[$col] = $this->request->getVar($col);
        }

        $this->model->insert($data);

        $errors = $this->model->errors();

        $ok = empty($errors);

        $response = [
            'status'   => 201,
            'error'    => !$ok,
            'messages' => [
                'success' => $ok ? 'Data Created' : false ,
                'error'   => $errors,
            ]
        ];

        return $this->respondCreated($response);
    }

    // // update 
    public function update($id = null)
    {
        foreach ($this->model->allowedFields as $col) {
            $data[$col] = $this->request->getVar($col);
        }
        
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

        return $this->respond($response);
    }

    // // delete
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
            $error = $this->model->db->error();

            // try {
            //code...
            $this->model->delete($id);
            // } catch (\Throwable $th) {
            // throw $th;

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

    //file upload
    public function uploadImage($field, $table, $id = null)
    {
        helper('text');

        $UPLOAD_DIR = FCPATH . 'uploads\\' . $table . '\\';

        $response = [
            'error' => false,
            'file' => null,
            'action' => null
        ];

        if($this->request->getVar($field) === '*delete*'){
            $row = $this->model->asObject()->find($id);

            if(!empty($row->$field)){
                $filename = $UPLOAD_DIR . $row->$field;
                unlink(realpath($filename));
            }

            $response = [
                'error' => false,
                'file' => null,
                'action' => 'delete'
            ];
            return $response;
        }

        if (empty($_FILES[$field]))
            return $response;

        $extension = pathinfo($_FILES[$field]["name"], PATHINFO_EXTENSION);

        $filename = random_string('alnum', 16) . '.' . $extension;

        $targetPath = $UPLOAD_DIR . $filename;

        $response = [
            'error' => false,
            'file' => $filename,
            'action' => null
        ];

        try {
            //code...
            move_uploaded_file($_FILES[$field]['tmp_name'], $targetPath);
        } catch (\Exception $e) {
            //throw $th;

            $response = [
                'error' => $e->getMessage(),
            ];
        }

        return $response;
    }
}
