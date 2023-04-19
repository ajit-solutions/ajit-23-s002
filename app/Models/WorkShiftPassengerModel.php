<?php namespace App\Models;
  
  use CodeIgniter\Model;
    
  class WorkShiftPassengerModel extends Model
  {
      protected $table = 'work_shifts_passengers';
      protected $primaryKey = 'work_shift_passenger_id';
      protected $allowedFields = [
        'work_shift_id',
        'passenger_id',
      ];

      protected $returnType    = \App\Entities\WorkShiftPassenger::class;

    }