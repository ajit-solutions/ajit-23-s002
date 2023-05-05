<?php

namespace App\Models;

use CodeIgniter\Model;

class DriverModel extends Model
{
  protected $table = 'drivers';
  protected $primaryKey = 'driver_id';

  protected $allowedFields = [
    'driver_name',
    'driver_surname',
    'driver_id_card',
    'driver_photo',
    'driver_driving_license_photo',
    'driver_driving_license_expiration_date',
    'driver_available',
    'is_contractor'
  ];

  protected $returnType    = \App\Entities\Driver::class;

  protected $validationRules =  [
    'driver_name' => 'required|max_length[50]|alpha_space',
    'driver_surname' => 'required|max_length[100]|alpha_space',
    'driver_id_card' => 'required|is_unique[drivers.driver_id_card,driver_id_card,{driver_id}]|regex_match[/^[0-9]{3}-[0-9]{7}-[0-9]{1}$/]',
    'driver_photo' => 'max_length[255]',
    'driver_driving_license_photo' => 'max_length[255]',
    /* verificar si el today se lo traga ahí, el tema es que la fecha debe ser mayor que hoy 
    y los días definidos en seccion de configuracion, tratar eso */
    // 'driver_driving_license_expiration_date' => 'required|valid_date|greater_than[today]', 
    'driver_driving_license_expiration_date' => 'required|valid_date', 
    // 'driver_available' => 'required|in_list[Si,No]',
    // 'is_contractor' => 'required|in_list[Si,No]',
    // 'vehicle_id' => 'required|integer'
  ];

  protected $validationMessages = [
    'driver_name' => [
      'required' => 'El nombre del chofer no puede estar vacío.',
      'max_length' => 'El nombre del chofer no debe exceder los 50 caracteres.',
      'alpha_space' => 'El nombre del chofer solo acepta letras y espacios.',
    ],
    'driver_surname' => [
      'required' => 'El apellido del chofer no puede estar vacío.',
	    'max_length' => 'El apellido del chofer no debe exceder los 100 caracteres.',
      'alpha_space' => 'El apellido del chofer solo acepta letras y espacios.',
    ],
    'driver_id_card' => [
      'required' => 'El número de identificación del chofer no puede estar vacío.',
      'is_unique' => 'Este número de identificación ya está registrado.',
      'regex_match' => 'El número de cédula del contratista debe seguir el formato 123-1234567-1.',
    ],
    'driver_photo' => [
      'max_length' => 'La ruta de la foto del chofer no puede tener más de 255 caracteres.',
    ],
    'driver_driving_license_photo' => [
      'max_length' => 'La ruta de la foto de la licencia de conducir del chofer no puede tener más de 255 caracteres.',
    ],
    'driver_driving_license_expiration_date' => [
      'required' => 'La fecha de vencimiento de la licencia de conducir es obligatorio.',
	  'valid_date' => 'La fecha de vencimiento de la licencia de conducir no es válida.',
	  'greater_than' => 'La fecha de vencimiento de la licencia de conducir está por vencer',
    ],
    'driver_available' => [
      'required' => 'Debe seleccionar si el chofer está disponible o no.',
      'in_list' => 'La opción seleccionada para la disponibilidad del chofer no es válida.'
    ],
    'is_contractor' => [
      'required' => 'Debe seleccionar si el chofer es contratista o no.',
      'in_list' => 'La opción seleccionada para si el chofer es contratista o no no es válida.'
    ],
    'vehicle_id' => [
      'required' => 'Se requiere información sobre el vehículo.',
	  'integer' => 'El ID del vehículo debe ser un número entero.',
    ]
  ];

  public function getAll()
  {
    return $this
      ->asObject()
      ->where('is_contractor', 'No')
      ->orderBy('driver_name', 'driver_surname')
      ->findAll();
  }
}
