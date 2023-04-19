<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\ClientModel;

use Config\App;

class RouteModel extends Model
{
  protected $table = 'routes';
  protected $primaryKey = 'route_id';
  protected $returnType = \App\Entities\Route::class;
  protected $allowedFields = [
    'route_name',
    'route_color',
    'route_description',
    'route_end_location',
    'route_end_address',
    'client_id',
  ];

  protected $validationRules =  [
    'route_name' => 'required|max_length[50]',
    'route_color' => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
    'route_description' => 'permit_empty',
    'route_end_location' => 'permit_empty|valid_json',
    'route_end_address' => 'permit_empty',
    'client_id' => 'required|integer'
  ];

  protected $validationMessages = [
    'route_name' => [
      'required' => 'El nombre de la ruta es requerido.',
      'max_length' => 'El nombre de la ruta no puede tener más de 50 caracteres.'
    ],
    'route_color' => [
      'required' => 'El color de la ruta es requerido.',
      'regex_match' => 'El color de la ruta debe ser un valor hexadecimal válido, por ejemplo: #FF0000.'
    ],
    'client_id' => [
      'required' => 'El identificador del cliente es requerido.',
      'integer' => 'El identificador del cliente debe ser un número entero.'
    ]
  ];

  public function getAll()
  {
    $clientModel = new ClientModel();

    $items = $this
      ->asObject()
      ->orderBy('route_name')
      ->findAll();

    foreach ($items as &$item) {
      $client = $clientModel->find($item->client_id);
      $item->client_name = isset( $client->client_name) ?  $client->client_name : '';
    }
    return $items;
  }
}
