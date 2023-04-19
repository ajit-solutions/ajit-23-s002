<?php

namespace App\Models;

use CodeIgniter\Model;

use App\Models\ClientModel;
use App\Models\OfficeModel;
use App\Models\RouteModel;

class RateRouteModel extends Model
{
  protected $table = 'rates_routes';
  protected $primaryKey = 'rate_route_id';
  protected $allowedFields = [
    'client_id',
    'office_id',
    'route_id',
    'in_time_price',
    'out_of_time_price',
  ];

  protected $returnType    = \App\Entities\RateRoute::class;

  public function getAll()
  {
    $clientModel = new ClientModel();
    $officeModel = new OfficeModel();
    $routeModel = new RouteModel();

    $items = $this
      ->asObject()
      // ->orderBy('passenger_name', 'passenger_surname',)
      ->findAll();

    foreach ($items as &$item) {
      $client = $clientModel->find($item->client_id);
      $office = $officeModel->find($item->office_id);
      $route = $routeModel->find($item->route_id);

      $item->client_name = $client->client_name;
      $item->office_name = $office->office_name;
      $item->route_name = $route->route_name;
    }
    return $items;
  }
}
