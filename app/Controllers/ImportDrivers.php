<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\Generic;
use App\Models\DriverModel;

class ImportDrivers extends Generic
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
            'nombre' => 'driver_name', 
            'apellidos' => 'driver_surname', 
            'cedula' => 'driver_id_card', 
            'disponible' => 'driver_available',
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
                $numberOfFields = 4;

                $driverData = array();

                $csv_columns = array();
                
                while (($filedata = fgetcsv($file, 1000, $csv_delimiter)) !== FALSE) {
                    $num = count($filedata);
                    $row = array();

                    if($i > 0 && $num == $numberOfFields){ 
                        foreach ($filedata as $key => $value) {
                            # code...
                            $row[$columns[$csv_columns[$key]]] = $value;
                        }
                        
                        $driverData[$i]['driver_id'] = null;
                        $driverData[$i]['driver_name'] = $row['driver_name'];
                        $driverData[$i]['driver_surname'] = $row['driver_surname'];
                        $driverData[$i]['driver_id_card'] = $row['driver_id_card'];
                        $driverData[$i]['driver_available'] = $row['driver_available'];
                    }else{
                        $csv_columns = $filedata;
                        // var_dump($csv_columns, array_intersect(array_keys($columns), $csv_columns));
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

                // var_dump($driverData);
                foreach($driverData as $data){
                    $driverModel = new DriverModel();
                   
                    $driver_id_card = $driverData[$i]['driver_id_card'];
                    $drivers = $driverModel->getAll("driver_id_card = '$driver_id_card'");

                    // echo $driverModel->db->lastQuery;
                                   
                    // unset(
                    //     $driverData[$i]['driver_type']
                    //     $driverData[$i]['driver_type']
                    //     $driverData[$i]['driver_type']
                    // );

                    $driverData[$i]['uid'] = uniqid();

                    if(count($drivers) > 0){

                        $driver = $drivers[0];

                        // var_dump($driver);

                        $driverData[$i]['driver_related_id'] = $driver->driver_id;
                        $driverData[$i]['driver_photo'] = $driver->driver_photo;
                        $driverData[$i]['driver_driving_license_photo'] = $driver->driver_driving_license_photo;
                        $driverData[$i]['driver_driving_license_expiration_date'] = $driver->driver_driving_license_expiration_date;
                        $driverData[$i]['is_contractor'] = $driver->is_contractor;

                        array_push($data_to_import, $driver, $driverData[$i]);
                    } else {                      
                        $driverData[$i]['driver_related_id'] = null;
                        $driverData[$i]['driver_photo'] = null;
                        $driverData[$i]['driver_driving_license_photo'] = null;
                        $driverData[$i]['driver_driving_license_expiration_date'] = null;
                        $driverData[$i]['is_contractor'] = null;
                        
                        array_push($data_to_import, $driverData[$i]);
                    }

                    $i++;
                }
                //' rows successfully added.
            }
            else{
                //'CSV file coud not be imported.
            }
            }else{
                //'CSV file coud not be imported.
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

        $driverModel = new DriverModel();
        $defaultValidationRules = $driverModel->getValidationRules();

        foreach ($data as $k => $row) {
            # code...
            $validatioRules = $defaultValidationRules;
            $id = isset($row['driver_related_id']) ? $row['driver_related_id'] : null;

            $tuple  = array();
            foreach ($driverModel->allowedFields as $col) {
                $value = isset($row[$col]) ? trim($row[$col]) : null;
                if($col === 'driver_insurance_expiration_date' && $value){
                    $date_parts = explode('/', $value);
                    $value = "$date_parts[2]-$date_parts[1]-$date_parts[0]";
                }
                $tuple [$col] = empty($value) ? null : $value;
            }

            $action = 'Insertar';
            
            if($id){
                $validatioRules['driver_registration_number'] = str_replace('{driver_id}', $id, $validatioRules['driver_registration_number']);
                $driverModel->setValidationRules($validatioRules);

                $driverModel->update($id, $tuple );
                $action = 'Actualizar';

                $errors = $driverModel->errors();

            }else{
                $driverModel->insert($tuple );

                $errors = $driverModel->errors();

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
