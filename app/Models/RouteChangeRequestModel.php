<?php

namespace App\Models;

use CodeIgniter\Model;

class RouteChangeRequestModel extends Model
{
  protected $table = 'route_change_requests';
  protected $primaryKey = 'route_change_request_id';
  protected $allowedFields = [
    'passenger_id',
    'route_id',
    'route_change_request_date',
    'route_change_request_last_date',
    'route_change_request_accepted',
  ];

  protected $returnType    = \App\Entities\RouteChangeRequest::class;

  protected $validationRules = [
    'passenger_id' => 'required|integer',
    'route_id' => 'required|integer',
    // 'route_change_request_date' => 'required|valid_date|greater_than[today]',
    'route_change_request_date' => 'required|valid_date',
    'route_change_request_last_date' => 'required|valid_date',
  ];

  protected $validationMessages = [
    'passenger_id' => [
      'required' => 'El ID del pasajero es obligatorio.',
      'integer' => 'El ID del pasajero debe ser un número entero.'
    ],
    'route_id' => [
      'required' => 'El ID de la ruta es obligatorio.',
      'integer' => 'El ID de la ruta debe ser un número entero.'
    ],
    'route_change_request_date' => [
      'required' => 'La fecha de solicitud de cambio de ruta es obligatoria.',
      'valid_date' => 'La fecha del cambio de ruta solicitada no es válida.',
      'greater_than' => 'Por favor revise la fecha del cambio de ruta solicitada.',
    ],
    'route_change_request_last_date' => [
      'required' => 'La fecha de solicitud de cambio de ruta es obligatoria.',
      'valid_date' => 'La fecha del cambio de ruta solicitada no es válida.',
      'greater_than' => 'Por favor revise la fecha del cambio de ruta solicitada.',
    ],
  ];

  public function getAll()
  {
    $passengerModel = new PassengerModel();
    $routeModel = new RouteModel();

    $items = $this
      ->asObject()
      ->select('route_change_requests.route_change_request_id,
                route_change_requests.passenger_id,
                route_change_requests.route_id,
                route_change_requests.route_change_request_date,
                route_change_requests.route_change_request_last_date,
                route_change_requests.route_change_request_accepted,
          ')
      ->orderBy('route_change_requests.route_change_request_date')
      ->findAll();

    foreach ($items as &$item) {
      $passenger = $passengerModel->find($item->passenger_id);
      $previousRoute = $routeModel->find($passenger->route_id);
      $currentRoute = $routeModel->find($item->route_id);

      $item->passenger = $passenger;
      $item->previousRoute = $previousRoute;
      $item->currentRoute = $currentRoute;
    }
    return $items;
  }
}
