<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\Generic;
use App\Models\TripModel;
use App\Models\ExtendedTripModel;

class Trips extends Generic
{
    use ResponseTrait;

    protected $modelName = 'App\Models\TripModel';

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

        foreach ($this->model->allowedFields as $col) {
            $data[$col] = $this->request->getVar($col);
        }

        $trip_id = $this->model->insert($data);

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

        $rel_model = new \App\Models\TripPassengerModel();

        $passengers = $this->request->getVar('passengers');

        foreach ($passengers as $index => $passenger_id) {
            $rel_data = array(
                'trip_id' => $trip_id,
                'passenger_id' => $passenger_id
            );
            $rel_model->insert($rel_data);
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

        $rel_model = new \App\Models\TripPassengerModel();

        $rel_model->where('trip_id', $id)->delete();

        $passengers = $this->request->getVar('passengers');

        foreach ($passengers as $index => $passenger_id) {
            $rel_data = array(
                'trip_id' => $id,
                'passenger_id' => $passenger_id
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

            $this->model->delete($id);

            $error = $this->model->db->error();

            $rel_model = new \App\Models\TripPassengerModel();

            $rel_model->where('trip_id', $id)->delete();

            $response = [
                'status'   => 201,
                'code'     => $error['code'],
                'error'    => !!$error['code'],
                'messages' => [
                    'error' => $error['message'],
                    'success' => !$error['code'] ? 'Data Deleted' : null
                ]
            ];

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
