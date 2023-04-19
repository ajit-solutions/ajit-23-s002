<?php namespace App\Models;
  
  use CodeIgniter\Model;
    
  class EmployeeModel extends Model
  {
      protected $table = 'employees';
      protected $primaryKey = 'employee_id';
      protected $allowedFields = [
        'employee_name', 
        'employee_surname'
      ];
      
      protected $returnType    = \App\Entities\Employee::class;

      public function getAll()
      {
        return $this
          ->asObject()
          ->orderBy('employee_name', 'employee_surname')
          ->findAll();
      }
  
  }