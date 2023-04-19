<?php

namespace App\Models;

use CodeIgniter\Model;

use App\Models\RouteModel;
use App\Models\PassengerTypeModel;

class EventualPassengerModel extends Model
{
  protected $table = 'eventual_passengers';
  protected $primaryKey = 'eventual_passenger_id';
  protected $allowedFields = [
    'eventual_passenger_name',
    'eventual_passenger_surname',
    'eventual_passenger_id_card',
    'passenger_type_id',
    'route_id',
  ];

  protected $returnType    = \App\Entities\EventualPassenger::class;

  public function getAll()
  {
    $routeModel = new RouteModel();
    $passengerTypeModel = new PassengerTypeModel();

    $items = $this
      ->asObject()
      ->orderBy('eventual_passenger_name', 'eventual_passenger_surname',)
      ->findAll();

    foreach ($items as &$item) {
      $route = $routeModel->find($item->route_id);
      $type = $passengerTypeModel->find($item->passenger_type_id);

      $item->route_name = $route->route_name;
      $item->passenger_type_name = $type->passenger_type_name;
    }
    return $items;
  }
}
