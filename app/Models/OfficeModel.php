<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\ClientModel;

use Config\App;

class OfficeModel extends Model
{
  protected $table = 'offices';
  protected $primaryKey = 'office_id';
  protected $returnType = \App\Entities\Office::class;
  protected $allowedFields = [
    'office_name',
    'office_address',
    'office_location',
    'client_id'
  ];


  protected $validationRules =  [
    'office_name' => 'required|max_length[50]',
    'office_address' => 'required|max_length[255]',
    'office_location' => 'required|max_length[255]',
    'client_id' => 'required|integer',
  ];

  protected $validationMessages = [
    'office_name' => [
      'required' => 'El nombre de la oficina es obligatorio.',
      'max_length' => 'El nombre de la oficina no puede tener más de 50 caracteres.',
    ],
    'office_address' => [
      'required' => 'La dirección de la oficina es obligatoria.',
      'max_length' => 'La dirección de la oficina no puede tener más de 255 caracteres.',
    ],
    'office_location' => [
      'required' => 'La ubicación de la oficina es obligatoria.',
      'max_length' => 'La ubicación de la oficina no puede tener más de 255 caracteres.',
    ],
    'client_id' => [
      'required' => 'El cliente es obligatorio.',
      'integer' => 'El ID del cliente debe ser un número entero.',
    ],
  ];

  public function getAll()
  {
    $clientModel = new ClientModel();

    $items = $this
      ->asObject()
      ->orderBy('office_name')
      ->findAll();

    foreach ($items as &$item) {
      $client = $clientModel->find($item->client_id);
      $item->client_name = $client->client_name;
    }
    return $items;
  }
}
