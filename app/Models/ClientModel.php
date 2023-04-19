<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
  protected $table = 'clients';
  protected $primaryKey = 'client_id';
  protected $allowedFields = [
    'client_name',
    'client_rnc',
    'client_address',
    'client_location'
  ];

  protected $returnType    = \App\Entities\Client::class;

  protected $validationRules =  [
    'client_name' => 'required|max_length[50]|is_unique[clients.client_name,client_id,{client_id}]',
    'client_rnc' => 'required|regex_match[/^[0-9]{3}-[0-9]{5}-[0-9]{1}$/]',
    'client_address' => 'required',
    'client_location' => 'required',
  ];

  protected $validationMessages = [
    'client_name' => [
      'required' => 'El nombre del cliente es obligatorio.',
      'max_length' => 'El nombre del cliente no debe exceder los 50 caracteres.',
      'is_unique' => 'El nombre del cliente debe ser único.',
    ],
    'client_rnc' => [
      'required' => 'El RNC del cliente es obligatorio.',
      'regex_match' => 'El RNC del cliente debe seguir el formato 123-12345-1.',
    ],
    'client_address' => [
      'required' => 'La dirección del cliente es obligatoria.',
    ],
    'client_location' => [
      'required' => 'La ubicación del cliente es obligatoria.',
    ],
  ];
}
