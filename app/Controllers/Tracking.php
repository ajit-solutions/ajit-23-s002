<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\Generic;
use App\Models\TrackingModel;


class Tracking extends Generic
{
    use ResponseTrait;

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

    // public function Tracking()
    // {
    //     // $data = $this->model->orderBy('brand_name')->findAll();

    //     // return $this->respond($data);\prototypes\LWo6bqPPaxc0LRPoaM5R\player
    // }

    public function index()
    {
        // $query = $this->db->query('SELECT * FROM v_actives_trips');

        $trackingModel = new \App\Models\TrackingModel();

        $data = $trackingModel
            ->asObject()
            ->findAll();

        return $this->respond($data);
    }

    public function trackedVehicles ()
    {
        $upload_response = (object) $this->uploadImage('brand_logo', 'brands');


        foreach ($this->model->allowedFields as $col) {
            $value = !is_null($this->request->getVar($col)) ? trim($this->request->getVar($col)) : null;
            $data[$col] = empty($value) ? null : $value;
        }

        if (!$upload_response->error && $upload_response->file)
            $data['brand_logo'] = !$upload_response->error ? $upload_response->file : null;

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
                'upload'  => $upload_response->error
            ]
        ];

        return $this->respondCreated($response);
    }

}
