<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\Generic;
use App\Models\VehicleTypeModel;


class VehicleTypes extends Generic
{

    use ResponseTrait;

    protected $modelName = 'App\Models\VehicleTypeModel';
}
