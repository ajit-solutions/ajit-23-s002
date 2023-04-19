<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
  protected $table = 'users';
  protected $primaryKey = 'user_id';
  protected $allowedFields = [
    'user_name',
    'user_surname',
    'user_login',
    'user_password',
];

  protected $returnType    = \App\Entities\User::class;

  // Validation
  protected $validationRules =  [
    'user_name' => 'required',
    'user_login' => 'required|is_unique[users.user_login,user_id,{user_id}]',
    'user_password' => 'required|min_length[8]',
  ];

  protected $validationMessages = [
    'user_name' => [
      'required' => 'El campo Nombre es obligatorio.',
    ],
    'user_login' => [
      'required' => 'El campo Login es obligatorio.',
      'is_unique' => 'El campo Login no puede repetirse.',
    ],
    'user_password' => [
      'required' => 'El campo Password es obligatorio.',
      'min_length' => 'El campo Password debe tener al menos 8 caracteres.',
    ]
  ];
}
