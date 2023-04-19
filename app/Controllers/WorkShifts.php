<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\Generic;
use App\Models\WorkShiftPassengerModel;


class WorkShifts extends Generic
{
    use ResponseTrait;

    protected $modelName = 'App\Models\WorkShiftModel';

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

        $work_shift_id = $this->model->insert($data);

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

        $rel_model = new \App\Models\WorkShiftPassengerModel();

        $passengers = $this->request->getVar('work_shift_passengers');

        if ($passengers)
            foreach ($passengers as $index => $passenger_id) {
                $rel_data = array(
                    'work_shift_id' => $work_shift_id,
                    'passenger_id' => $passenger_id
                );
                $rel_model->insert($rel_data);

            }

        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Data Saved'
            ]
        ];
        return $this->respondCreated($response);
    }

    public function update($id = null)
    {

        foreach ($this->model->allowedFields as $col) {
            $data[$col] = $this->request->getVar($col);
        }

        $work_shift_id = $this->model->update($id, $data);

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

        $rel_model = new \App\Models\WorkShiftPassengerModel();

        $passengers = $this->request->getVar('work_shift_passengers');

        $rel_model->where('work_shift_id', $id)->delete();

        if ($passengers)
            foreach ($passengers as $index => $passenger_id) {
                $rel_data = array(
                    'work_shift_id' => $id,
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
}
