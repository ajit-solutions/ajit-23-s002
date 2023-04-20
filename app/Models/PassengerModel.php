<?php

namespace App\Models;

use CodeIgniter\Model;

use App\Models\ClientModel;
use App\Models\OfficeModel;
use App\Models\RouteModel;

class PassengerModel extends Model
{
  protected $table = 'passengers';
  protected $primaryKey = 'passenger_id';
  protected $allowedFields = [
    'passenger_name',
    'passenger_surname',
    'passenger_address',
    'passenger_id_card',
    'passenger_type_id',
    'passenger_number',
    'passenger_location',
    'client_id',
    'office_id',
    'route_id',
  ];

  protected $returnType    = \App\Entities\Passenger::class;

  protected $validationRules = [
    'passenger_name' => 'required|max_length[50]',
    'passenger_surname' => 'required|max_length[100]',
    'passenger_address' => 'required|max_length[255]',
    'passenger_id_card' => 'required|regex_match[/^[0-9]{3}-[0-9]{7}-[0-9]{1}$/]|is_unique[passengers.passenger_id_card,passenger_id,{passenger_id}]',
    'passenger_type_id' => 'required',
    'passenger_number' => 'required|is_natural_no_zero|less_than[999999]',
    'passenger_location' => 'required|max_length[255]',
    'client_id' => 'required|integer',
    'office_id' => 'required|integer',
    'route_id' => 'required|integer',
  ];
  
  protected $validationMessages = [
    'passenger_name' => [
      'required' => 'El nombre del pasajero es obligatorio.',
      'max_length' => 'El nombre del pasajero no puede tener más de 50 caracteres.'
    ],
    'passenger_surname' => [
      'max_length' => 'El apellido del pasajero no puede tener más de 100 caracteres.'
    ],
    'passenger_address' => [
      'required' => 'La dirección del pasajero es obligatoria.',
      'max_length' => 'La dirección del pasajero no puede tener más de 255 caracteres.'
    ],
    'passenger_id_card' => [
      'required' => 'El número de identificación del pasajero es obligatorio.',
      'regex_match' => 'El número de identificación del pasajero debe tener el formato 123-1234567-1.',
      'is_unique' => 'El número de identificación del pasajero ya existe en la base de datos.'
    ],
    'passenger_type_id' => [
      'required' => 'El tipo de pasajero es obligatorio.',
      'in_list' => 'La opción seleccionada para el tipo de pasajero no es válida.'
    ],
    'passenger_number' => [
      'required' => 'El número de pasajero es obligatorio.',
      'is_natural_no_zero' => 'El número de pasajero tiene que ser mayor que cero.',
      'less_than' => 'El número de pasajero no puede tener más de 6 dígitos.'
    ],
    'passenger_location' => [
      'required' => 'La ubicación del pasajero es obligatoria.',
      'max_length' => 'La ubicación del pasajero no puede tener más de 255 caracteres.'
    ],
    'client_id' => [
      'required' => 'El ID del cliente es obligatorio.',
      'integer' => 'El ID del cliente debe ser un número entero.'
    ],
    'office_id' => [
      'required' => 'El ID de la oficina es obligatorio.',
      'integer' => 'El ID de la oficina debe ser un número entero.'
    ],
    'route_id' => [
      'required' => 'El ID de la ruta es obligatorio.',
      'integer' => 'El ID de la ruta debe ser un número entero.'
    ]
  ];

  public function getAll()
  {
    $clientModel = new ClientModel();
    $officeModel = new OfficeModel();
    $routeModel = new RouteModel();

    $items = $this
      ->asObject()
      ->orderBy('passenger_name', 'passenger_surname',)
      ->findAll();

    foreach ($items as &$item) {
      $client = $clientModel->find($item->client_id);
      $office = $officeModel->find($item->office_id);
      $route = $routeModel->find($item->route_id);

      $item->client_name = $client ? $client->client_name : '';
      $item->office_name = $office ? $office->office_name : '';
      $item->route_name = $route ? $route->route_name : '';
    }
    return $items;
  }
}
