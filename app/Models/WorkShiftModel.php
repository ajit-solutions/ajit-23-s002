<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkShiftModel extends Model
{
  protected $table = 'work_shifts';
  protected $primaryKey = 'work_shift_id';
  protected $allowedFields = [
    'work_shift_datetime',
    'work_shift_trip_duration',
    'client_id',
    'office_id',
  ];

  protected $returnType    = \App\Entities\WorkShift::class;

  protected $validationRules =  [
    'work_shift_datetime' => 'required|valid_date',
    // 'work_shift_trip_duration' => 'required|integer',
    'client_id' => 'required|integer',
    'office_id' => 'required|integer',
  ];

  protected $validationMessages = [
    'work_shift_datetime' => [
      'required' => 'El campo fecha y hora es obligatorio.',
      'valid_date' => 'El campo fecha y hora debe ser una fecha y hora válida.',
    ],
    'work_shift_trip_duration' => [
      'required' => 'El campo duración del viaje es obligatorio.',
      'integer' => 'El campo duración del viaje debe ser un número entero.',
    ],
    'client_id' => [
      'required' => 'El campo cliente es obligatorio.',
      'integer' => 'El campo cliente debe ser un número entero.',
    ],
    'office_id' => [
      'required' => 'El campo oficina es obligatorio.',
      'integer' => 'El campo oficina debe ser un número entero.',
    ],
  ];

  public function getAll()
  {
    $passengerModel = new PassengerModel();

    $query = $this->db->query("SELECT `work_shifts`.`work_shift_id`, `work_shifts`.`client_id`, `work_shifts`.`office_id`,
        `work_shifts`.`work_shift_datetime`, GROUP_CONCAT(work_shifts_passengers.passenger_id) as work_shift_passengers_id,
        `work_shifts`.`work_shift_trip_duration`, `clients`.`client_name`, `clients`.`client_rnc`, `offices`.`office_name`
        FROM `work_shifts`
        LEFT JOIN `work_shifts_passengers` ON `work_shifts_passengers`.`work_shift_id` = `work_shifts`.`work_shift_id`
        LEFT JOIN `clients` ON `clients`.`client_id` = `work_shifts`.`client_id`
        LEFT JOIN `offices` ON `offices`.`office_id` = `work_shifts`.`office_id`
        GROUP BY `work_shifts`.`work_shift_id`
        ORDER BY `work_shift_datetime`");

    $items = $query->getResult();

    foreach ($items as &$item) {

      $work_shift_passengers = $item->work_shift_passengers_id;

      $passengers = is_null($work_shift_passengers) ? array() : $passengerModel
        ->asObject()
        ->where("passenger_id IN ($work_shift_passengers)")
        ->findAll();

      // var_dump($this->db->lastQuery);

      $item->work_shift_passengers = $passengers;
    }
    return $items;
  }
}
