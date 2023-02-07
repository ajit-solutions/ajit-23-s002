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
