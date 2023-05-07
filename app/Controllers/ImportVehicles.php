<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\Generic;
use App\Models\VehicleModel;
use App\Models\VehicleStatusModel;
use App\Models\BrandModel;
use App\Models\VehicleTypeModel;

class ImportVehicles extends Generic
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

    protected $modelName = 'App\Models\BrandModel';

    public function preview()
    {
        $columns = array(
            'marca' => 'brand_name', 
            'matricula' => 'vehicle_registration_number', 
            'tipo' => 'vehicle_type_name', 
            'capacidad' => 'vehicle_capacities', 
            'estado' => 'vehicle_status_name', 
            'id-poliza' => 'vehicle_insurance_policy_number',	
            'comp-seguro' => 'vehicle_insurance_company', 
            'fecha-venc' => 'vehicle_insurance_expiration_date',	
            'nombre del asegurado' => 'vehicle_insurance_beneficiary',
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

                $vehicleData = array();
                $brandData = array();
                $vehicleTypeData = array();
                $vehicleStatusData = array();

                $csv_columns = array();
                
                while (($filedata = fgetcsv($file, 1000, $csv_delimiter)) !== FALSE) {
                    $num = count($filedata);
                    $row = array();



                    if($i > 0 && $num == $numberOfFields){ 
                        foreach ($filedata as $key => $value) {
                            # code...
                            $row[$columns[$csv_columns[$key]]] = $value;
                        }
                        
                        $vehicleData[$i]['vehicle_id'] = null;
                        $vehicleData[$i]['vehicle_registration_number'] = $row['vehicle_registration_number'];
                        $vehicleData[$i]['vehicle_type_name'] = $row['vehicle_type_name'];
                        $vehicleData[$i]['vehicle_capacities'] = $row['vehicle_capacities'];
                        $vehicleData[$i]['vehicle_status_name'] = $row['vehicle_status_name'];
                        $vehicleData[$i]['vehicle_insurance_policy_number'] = $row['vehicle_insurance_policy_number'];
                        $vehicleData[$i]['vehicle_insurance_company'] = $row['vehicle_insurance_company'];
                        $vehicleData[$i]['vehicle_insurance_expiration_date'] = $row['vehicle_insurance_expiration_date'];
                        $vehicleData[$i]['vehicle_insurance_beneficiary'] = $row['vehicle_insurance_beneficiary'];
                        $vehicleData[$i]['brand_name'] = $row['brand_name'];

                        $brandData[$i]['brand_name'] = $row['brand_name'];
                        $vehicleTypeData[$i]['vehicle_type_name'] = $row['vehicle_type_name'];
                        $vehicleStatusData[$i]['vehicle_status_name'] = $row['vehicle_status_name'];
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

                foreach($vehicleData as $data){
                    $brandModel = new BrandModel();
                    $vehicleModel = new VehicleModel();
                    $vehicleTypeModel = new VehicleTypeModel();
                    $vehicleStatusModel = new VehicleStatusModel();

                    $brands = $brandModel->asObject()->where('brand_name', $brandData[$i]['brand_name'])->find();
                    $types = $vehicleTypeModel->asObject()->where('vehicle_type_name', $vehicleTypeData[$i]['vehicle_type_name'])->find();
                    $statuses = $vehicleStatusModel->asObject()->where('vehicle_status_name', $vehicleStatusData[$i]['vehicle_status_name'])->find();
                    
                    $vehicle_registration_number = $vehicleData[$i]['vehicle_registration_number'];
                    $vehicles = $vehicleModel->getAll("vehicle_registration_number = '$vehicle_registration_number'");
                                   
                    if(count($brands) === 0){
                        $brandData[$i]['brand_logo'] = null;

                        $brand_id = $brandModel->insert($brandData[$i]);
                    } else {
                        $brand = $brands[0];
                        $brand_id = $brand->brand_id;
                    }

                    if(count($types) === 0){
                    }else {
                        $type = $types[0];
                        $vehicle_type_id = $type->vehicle_type_id;
                    }

                    if(count($statuses) === 0){
                    }else {
                        $status = $statuses[0];
                        $vehicle_status_id = $status->vehicle_status_id;
                    }

                    // unset(
                    //     $vehicleData[$i]['vehicle_type']
                    //     $vehicleData[$i]['vehicle_type']
                    //     $vehicleData[$i]['vehicle_type']
                    // );

                    $vehicleData[$i]['vehicle_type_id'] = $vehicle_type_id;
                    $vehicleData[$i]['vehicle_status_id'] = $vehicle_status_id;
                    $vehicleData[$i]['brand_id'] = $brand_id;
                    $vehicleData[$i]['uid'] = uniqid();

                    if(count($vehicles) > 0){

                        $vehicle = $vehicles[0];

                        $vehicleData[$i]['vehicle_related_id'] = $vehicle->vehicle_id;
                        $vehicleData[$i]['contractor_id'] = $vehicle->contractor_id;

                        array_push($data_to_import, $vehicle, $vehicleData[$i]);
                    } else {                      
                        $vehicleData[$i]['vehicle_related_id'] = null;
                        $vehicleData[$i]['contractor_id'] = null;
                        array_push($data_to_import, $vehicleData[$i]);
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

        $vehicleModel = new VehicleModel();
        $defaultValidationRules = $vehicleModel->getValidationRules();

        foreach ($data as $k => $row) {
            # code...
            $validatioRules = $defaultValidationRules;
            $id = isset($row['vehicle_related_id']) ? $row['vehicle_related_id'] : null;

            $tuple  = array();
            foreach ($vehicleModel->allowedFields as $col) {
                $value = isset($row[$col]) ? trim($row[$col]) : null;
                if($col === 'vehicle_insurance_expiration_date' && $value){
                    $date_parts = explode('/', $value);
                    $value = "$date_parts[2]-$date_parts[1]-$date_parts[0]";
                }
                $tuple [$col] = empty($value) ? null : $value;
            }

            $action = 'Insertar';
            
            if($id){
                $validatioRules['vehicle_registration_number'] = str_replace('{vehicle_id}', $id, $validatioRules['vehicle_registration_number']);
                $vehicleModel->setValidationRules($validatioRules);

                $vehicleModel->update($id, $tuple );
                $action = 'Actualizar';

                $errors = $vehicleModel->errors();

            }else{
                $vehicleModel->insert($tuple );

                $errors = $vehicleModel->errors();

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
