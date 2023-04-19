<?php namespace App\Models;
  
  use CodeIgniter\Model;
    
  class TripPassengerModel extends Model
  {
      protected $table = 'trip_passengers';
      protected $primaryKey = 'trip_passenger_id';
      protected $allowedFields = [
        'trip_id',
        'passenger_id',
      ];

      protected $returnType    = \App\Entities\TripPassenger::class;

    }