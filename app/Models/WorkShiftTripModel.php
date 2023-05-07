<?php namespace App\Models;
  
  use CodeIgniter\Model;
    
  class WorkShiftModel extends Model
  {
      protected $table = 'work_shifts_trips';
      protected $primaryKey = 'work_shift_trip_id';
      protected $allowedFields = [
        'work_shift_id',
        'work_shift_datetime',
        'work_shift_trip_duration',
        'client_id',
        'office_id',
    ];

      protected $returnType    = \App\Entities\WorkShift::class;

      public function getAll()
      {
        $passengerModel = new PassengerModel();
    
        $items = $this
          ->asObject()
          ->join('work_shifts_passengers', 'work_shifts_passengers.work_shift_id = work_shifts.work_shift_id', 'LEFT')
          ->join('clients', 'clients.client_id = work_shifts.client_id', 'LEFT')
          ->join('offices', 'offices.office_id = work_shifts.office_id', 'LEFT')
          ->select('work_shifts.work_shift_id, 
          work_shifts.client_id,
          work_shifts.office_id,
          work_shifts.work_shift_datetime,
          GROUP_CONCAT(work_shifts_passengers.passenger_id) as work_shift_passengers_id,
          work_shifts.work_shift_trip_duration,
          clients.client_name,
          clients.client_rnc,
          offices.office_name')
          ->orderBy('work_shift_datetime')
          ->groupBy('work_shifts.work_shift_id')
          ->findAll();

        foreach ($items as &$item) {

          $work_shift_passengers = $item->work_shift_passengers_id;

          $passengers = is_null($work_shift_passengers) ? array() : $passengerModel
            ->asObject()
            ->where("passenger_id IN ($work_shift_passengers)")
            ->findAll();

          $item->work_shift_passengers = $passengers;
        }
        return $items;
      }
    }