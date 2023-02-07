<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Generic extends ResourceController
{

    use ResponseTrait;


    protected $modelName;
    protected $extendedModelName;
    protected $libraryName;
    // get all
    public function index()
    {
        $model = new $this->modelName;
        // $lib = isset($this->libraryName) ? new $this->libraryName : false;
        // $data = $lib ? $lib->getAll() : $model->findAll();
        $data = $model->getAll();

        return $this->respond($data);
    }

    // get single
    public function show($id = null)
    {
        $model = $this->extendedModelName ? new $this->extendedModelName : $this->model;

        $data = $model->getWhere([$this->model->primaryKey => $id])->getResult();
        if ($data) {
            return $this->respond($data);
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
        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Data Saved'
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

        $this->model->update($id, $data);
        $response = [
            'status'   => 200,
            'error'    => null,
            'messages' => [
                'success' => 'Data Updated'
            ]
        ];
        return $this->respond($response);
    }

    // // delete
    public function delete($id = null)
    {
        $data = $this->model->find($id);
        if ($data) {
            $this->model->delete($id);
            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'Data Deleted'
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    //file upload
    public function uploadImage($field)
    {
        $file = $this->request->getFile($field);

        $profile_image = $file->getName();

        // Renaming file before upload
        $temp = explode(".", $profile_image);
        $newfilename = round(microtime(true)) . '.' . end($temp);
        $fileDir = "./resources/images/{$this->model->table}/";
        $filePath = $fileDir . $newfilename;

        if ($file->move($fileDir, $newfilename)) {

            $data = [
                "fileName" => $newfilename,
                "filePath" => $filePath
            ];

            $response = [
                'status' => 200,
                'error' => false,
                'message' => "File uploaded successfully",
                'data' => $data
            ];
        } else {

            $response = [
                'status' => 500,
                'error' => true,
                'message' => 'Failed to upload image',
                'data' => []
            ];
        }

        return $this->respondCreated($response);
    }
}
