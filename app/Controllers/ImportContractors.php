<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\Generic;
use App\Models\PassengerModel;
use App\Models\ClientModel;
use App\Models\OfficeModel;
use App\Models\RouteModel;
use App\Models\PassengerTypeModel;


class ImportContractors extends Generic
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

    public function preview()
    {
        $columns = array(
            'nombre' => 'passenger_name', 
            'apellidos' => 'passenger_surname',	
            'cedula' => 'passenger_id_card', 
            'tipo' => 'passenger_type_name', 
            'id_empleado' => 'passenger_number', 
            'Cliente' => 'client_name',	
            'Oficina' => 'office_name', 
            'direccion' => 'passenger_address',	
            'ruta' => 'route_name'
        );
        $csv_scope = $this->request->getVar('csv_scope');
        $csv_delimiter = $this->request->getVar('csv_delimiter');

        $response = [
            'status'   => 201,
            'error'    => false,
            'messages' => [
                'success' => 'Datos importados;',
                'error'   => []
            ]
        ];

        $data_to_import = array();        
        $input = $this->validate([
            'csv_file' => 'uploaded[csv_file]|max_size[csv_file,2048]|ext_in[csv_file,csv],'
        ]);
        if (!$input) {
            $response = [
                'status'   => 201,
                'error'    => false,
                'messages' => [
                    'success' => 'Datos importados;',
                    'error'   => 'No input file.'
                ]
            ];
            return $this->respondCreated($response);
        }else{
            if($file = $this->request->getFile('csv_file')) {
            if ($file->isValid() && ! $file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move('../public/uploads/csvfile', $newName);
                $file = fopen("../public/uploads/csvfile/".$newName,"r");
                $i = 0;
                $numberOfFields = 9;

                $passengerData = array();
                $clientData = array();
                $officeData = array();
                $routeData = array();

                $csv_columns = array();
                
                while (($filedata = fgetcsv($file, 1000, $csv_delimiter)) !== FALSE) {
                    $num = count($filedata);
                    $row = array();



                    if($i > 0 && $num == $numberOfFields){ 
                        foreach ($filedata as $key => $value) {
                            # code...
                            $row[$columns[$csv_columns[$key]]] = $value;
                        }
                        
                        $passengerData[$i]['passenger_id'] = null;
                        $passengerData[$i]['passenger_name'] = $row['passenger_name'];
                        $passengerData[$i]['passenger_surname'] = $row['passenger_surname'];
                        $passengerData[$i]['passenger_id_card'] = $row['passenger_id_card'];
                        $passengerData[$i]['passenger_type_name'] = $row['passenger_type_name'];
                        $passengerData[$i]['passenger_number'] = $row['passenger_number'];
                        $passengerData[$i]['passenger_address'] = $row['passenger_address'];
                        $passengerData[$i]['client_name'] = $row['client_name'];
                        $passengerData[$i]['office_name'] = $row['office_name'];
                        $passengerData[$i]['route_name'] = $row['route_name'];

                        $clientData[$i]['client_name'] = $row['client_name'];
                        $officeData[$i]['office_name'] = $row['office_name'];
                        $routeData[$i]['route_name'] = $row['route_name'];
                    }else{
                        $csv_columns = $filedata;
                        if(count($columns) !== count($csv_columns) || count(array_intersect(array_keys($columns), $csv_columns)) !== count($columns)){

                            $response = [
                                'status'   => 201,
                                'error'    => true,
                                'messages' => [
                                    'success' => NULL,
                                    'error'   => array(
                                        'errors' => 'La estuctura del archivo no es la correcta.',
                                        'action' => '',
                                        'data' => [],
                                        'row_number' => 1
                                    )
                                ]
                            ];
                            return $this->respondCreated($response);
                       }
                    }
                    $i++;
                }
                fclose($file);
                $count = 0;
                $i = 1;

                foreach($passengerData as $data){
                    $passengerModel = new PassengerModel();
                    $clientModel = new ClientModel();
                    $officeModel = new OfficeModel();
                    $routeModel = new RouteModel();
                    $passengerTypeModel = new PassengerTypeModel();

                    $clients = $clientModel->asObject()->where('client_name', $clientData[$i]['client_name'])->find();
                    $offices = $officeModel->asObject()->where('office_name', $officeData[$i]['office_name'])->find();
                    $routes = $routeModel->asObject()->where('route_name', $routeData[$i]['route_name'])->find();
                    
                    $passenger_id_card = $passengerData[$i]['passenger_id_card'];
                    $passengers = $passengerModel->getAll("passenger_id_card = '$passenger_id_card'");
                  
                    $passengerTypes = $passengerTypeModel->asObject()->where('passenger_type_name', $passengerData[$i]['passenger_type_name'])->find();
                   
                    if(count($clients) === 0){
                        $clientData[$i]['client_rnc'] = '000-00000-0';
                        $clientData[$i]['client_address'] = '***';
                        $clientData[$i]['client_location'] = '{}';

                        $client_id = $clientModel->insert($clientData[$i]);
                    } else {
                        $client = $clients[0];
                        $client_id = $client->client_id;
                    }

                    if(count($offices) === 0){
                        $officeData[$i]['client_id'] = $client_id;
                        $officeData[$i]['office_address'] = '***';
                        $officeData[$i]['office_location'] = '{}';

                        $office_id = $officeModel->insert($officeData[$i]);

                    }else {
                        $office = $offices[0];
                        $office_id = $office->office_id;
                    }

                    if(count($passengerTypes) === 0){
                    }else {
                        $passengerType = $passengerTypes[0];
                        $passenger_type_id = $passengerType->passenger_type_id;
                    }

                    if(count($routes) === 0){
                        $routeData[$i]['client_id'] = $client_id;
                        $routeData[$i]['route_color'] = '#000000';
                        $routeData[$i]['route_description'] = '';
                        $routeData[$i]['route_end_location'] = '{}';
                        $routeData[$i]['route_end_address'] = '***';

                        $route_id = $routeModel->insert($routeData[$i]);

                    }else {
                        $route = $routes[0];
                        $route_id = $route->route_id;
                    }

                    unset($passengerData[$i]['passenger_type']);

                    $passengerData[$i]['passenger_type_id'] = $passenger_type_id;
                    $passengerData[$i]['passenger_location'] = '{}';
                    $passengerData[$i]['client_id'] = $client_id;
                    $passengerData[$i]['office_id'] = $office_id;
                    $passengerData[$i]['route_id'] = $route_id;
                    $passengerData[$i]['uid'] = uniqid();

                    if(count($passengers) > 0){

                        $passenger = $passengers[0];

                        $passengerData[$i]['passenger_related_id'] = $passenger->passenger_id;

                        array_push($data_to_import, $passenger, $passengerData[$i]);
                    } else {                      
                        $passengerData[$i]['passenger_related_id'] = null;
                        array_push($data_to_import, $passengerData[$i]);
                    }

                    $i++;
                }
                session()->setFlashdata('message', $count.' rows successfully added.');
                session()->setFlashdata('alert-class', 'alert-success');
            }
            else{
                session()->setFlashdata('message', 'CSV file coud not be imported.');
                session()->setFlashdata('alert-class', 'alert-danger');
            }
            }else{
            session()->setFlashdata('message', 'CSV file coud not be imported.');
            session()->setFlashdata('alert-class', 'alert-danger');
            }
        }
        $response = [
            'status'   => 201,
            'error'    => false,
            'messages' => [
                'success' => 'Datos a importar.',
                'data'   => $data_to_import
            ]
        ];     
        
        return $this->respondCreated($response);
    }

    public function import()
    {
        $errors_list = array();
        $data = $this->request->getVar('data');

        $passengerModel = new PassengerModel();
        $defaultValidationRules = $passengerModel->getValidationRules();

        foreach ($data as $k => $row) {
            # code...
            $validatioRules = $defaultValidationRules;
            $id = isset($row['passenger_related_id']) ? $row['passenger_related_id'] : null;

            $tuple  = array();
            foreach ($passengerModel->allowedFields as $col) {
                $value = !is_null($row[$col]) ? trim($row[$col]) : null;
                $tuple [$col] = empty($value) ? null : $value;
            }

            if($tuple['passenger_number'] === '-1' ){

                $keys = array_keys($validatioRules);
                $index = array_search('passenger_number', $keys);

                if(!is_bool($index)){
                    array_splice($validatioRules, $index, 1);
                    $passengerModel->setValidationRules($validatioRules);
                }
            }

            $action = 'Insertar';
            
            if($id){
                $validatioRules['passenger_id_card'] = str_replace('{passenger_id}', $id, $validatioRules['passenger_id_card']);
                $passengerModel->setValidationRules($validatioRules);

                $passengerModel->update($id, $tuple );
                $action = 'Actualizar';

                $errors = $passengerModel->errors();

            }else{
                $passengerModel->insert($tuple );

                $errors = $passengerModel->errors();

            }
            
            if(!empty($errors)){
                array_push($errors_list, array(
                    'errors' => $errors,
                    'action' => $action,
                    'data' => $tuple,
                    'row_number' => $k + 2
                ));
            }            
        }

        $ok = !count($errors_list);

        $response = [
            'status'   => 201,
            'error'    => !$ok,
            'messages' => [
                'success' => $ok ? 'Datos importados' : 'Errores detectados',
                'error'   => $errors_list,
            ]
        ];

        return $this->respondCreated($response);
    }
}
