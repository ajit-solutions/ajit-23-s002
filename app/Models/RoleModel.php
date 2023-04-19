<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
  protected $table = 'roles';
  protected $primaryKey = 'role_id';
  protected $allowedFields = [
    'role_name',
    'role_description'
  ];
  protected $returnType = \App\Entities\Role::class;

  // Validation
  protected $validationRules =  [
    'role_name' => 'required|is_unique[roles.role_name,role_id,{role_id}]',
  ];

  protected $validationMessages = [
    'role_name' => [
      'required' => 'El campo Rol es obligatorio',
      'is_unique' => 'El campo Rol no puede repetirse.',
    ],
  ];

}
