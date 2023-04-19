<?php namespace App\Models;
  
  use CodeIgniter\Model;
    
  class ConfigNotificationModel extends Model
  {
      protected $table = 'config_notifications';
      protected $primaryKey = 'config_notification_id';
      protected $allowedFields = [
        'internal_vehicles_maintenance_routines_notification_km',
        'speed_limit',
        'drivers_speeding_report',
        'automatically_register_arrival_parking',
        'system_link_loss_notification',
        'system_link_loss_notification_time',
        'notify_daytime_out_of_time_hours_trips',
        'notify_night_time_out_of_time_hours_trips',
        'license_expiration_notification_time',
      ];
      protected $returnType    = \App\Entities\ConfigNotification::class;
  
  }