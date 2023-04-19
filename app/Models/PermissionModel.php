<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
  protected $table = 'permissions';
  protected $primaryKey = 'permission_id';
  protected $allowedFields = [
    'controller',
    'controller_alias',
    'method',
    'method_alias',
  ];
  
  protected $returnType = \App\Entities\Permission::class;


}
