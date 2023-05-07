<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\Generic;
use App\Models\ZoneClientModel;
use App\Models\ClientModel;


class Zones extends Generic
{
    use ResponseTrait;

    protected $modelName = 'App\Models\ZoneModel';

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
        $zoneClientModel = new \App\Models\ZoneClientModel();
        $clientModel = new \App\Models\ClientModel();

        $data = $this->model
            ->asObject()
            ->join('exclusion_zone_client', 'exclusion_zones.exclusion_zone_id = exclusion_zone_client.exclusion_zone_id', 'LEFT')
            ->select('exclusion_zones.exclusion_zone_id,
         GROUP_CONCAT(exclusion_zone_client.client_id) as exclusion_zone_clients_id,
         exclusion_zones.exclusion_zone_name,
         exclusion_zones.exclusion_zone_description,
         exclusion_zones.exclusion_zone_polygons,
         exclusion_zones.exclusion_zone_general')
            ->orderBy('exclusion_zone_name')
            ->groupBy('exclusion_zones.exclusion_zone_id')
            ->findAll();

        $clients = [];

        foreach ($data as &$item) {

            $zoneClients = $item->exclusion_zone_clients_id;

            $clients = is_null($zoneClients) ? array() : $clientModel
                ->asObject()
                ->where("client_id IN ($zoneClients )")
                ->findAll();

            $item->exclusion_zone_clients = $clients;
        }

        return $this->respond($data);
    }

    public function create()
    {

        foreach ($this->model->allowedFields as $col) {
            $data[$col] = $this->request->getVar($col);
        }

        $zone_id = $this->model->insert($data);

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

        $rel_model = new \App\Models\ZoneClientModel();

        $clients = $this->request->getVar('exclusion_zone_clients');

        if (!empty($clients))
            foreach ($clients as $index => $client_id) {
                $rel_data = array(
                    'exclusion_zone_id' => $zone_id,
                    'client_id' => $client_id
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

        $rel_model = new \App\Models\ZoneClientModel();

        $clients = $this->request->getVar('exclusion_zone_clients');

        $rel_model->where('exclusion_zone_id', $id)->delete();

        if (!empty($clients))
            foreach ($clients as $index => $client_id) {
                $rel_data = array(
                    'exclusion_zone_id' => $id,
                    'client_id' => $client_id
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
