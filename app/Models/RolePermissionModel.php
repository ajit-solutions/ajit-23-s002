<?php

namespace App\Models;

use CodeIgniter\Model;

class RolePermissionModel extends Model
{
  protected $table = 'role_permissions';
  protected $primaryKey = 'role_permission_id';
  protected $allowedFields = [
    'role_id',
    'permission_id'
  ];
  
  protected $returnType = \App\Entities\RolePermission::class;

  public function getAll()
  {
    $permissionsModel = new PermissionModel() ;

    $query = $this->db->query("SELECT DISTINCT `roles`.`role_id`, `roles`.`role_name`
        FROM `roles`
        JOIN `role_permissions` ON `role_permissions`.`role_id` = `roles`.`role_id`
        ORDER BY `role_name`");

    $roles = $query->getResult();

    foreach ($roles as &$item) {

      $query = $this->db->query(" SELECT `role_permissions`.`role_permission_id`, `role_permission_id`, `permissions`.`permission_id`, `permissions`.`controller_alias`,`permissions`.`method_alias`
        FROM `role_permissions`
        LEFT JOIN `permissions` ON `permissions`.`permission_id` = `role_permissions`.`permission_id`
        WHERE `role_permissions`.`role_id`= $item->role_id
        ORDER BY `permissions`.`controller_alias`,`permissions`.`method_alias`
      ");

      $item->permissions = $query->getResult();
    }
    return $roles;
  }
}