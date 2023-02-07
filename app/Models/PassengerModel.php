<?php

namespace App\Models;

use CodeIgniter\Model;

use App\Models\ClientModel;
use App\Models\OfficeModel;

class PassengerModel extends Model
{
  protected $table = 'passengers';
  protected $primaryKey = 'passenger_id';
  protected $allowedFields = [
    'passenger_name',
    'passenger_surname',
    'passenger_address',
    'passenger_id_card',
    'passenger_type',
    'passenger_number',
    'passenger_location',
    'client_id',
    'office_id',
  ];

  protected $returnType    = \App\Entities\Passenger::class;

  public function getAll()
  {
    $clientModel = new ClientModel();
    $officeModel = new OfficeModel();

    $items = $this
      ->asObject()
      ->orderBy('passenger_name', 'passenger_surname',)
      ->findAll();

    foreach ($items as &$item) {
      $client = $clientModel->find($item->client_id);
      $office = $officeModel->find($item->office_id);

      $item->client_name = $client->client_name;
      $item->office_name = $office->office_name;
    }
    return $items;
  }
}
