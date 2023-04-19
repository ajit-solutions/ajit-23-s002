<?php

namespace App\Models;

use CodeIgniter\Model;

class BrandModel extends Model
{
  protected $table = 'brands';
  protected $primaryKey = 'brand_id';
  protected $allowedFields = [
    'brand_name',
    // 'brand_logo'
  ];
  protected $returnType = \App\Entities\Brand::class;

  // Validation
  protected $validationRules =  [
    'brand_name' => 'required|is_unique[brands.brand_name,brand_id,{brand_id}]|alpha_numeric_punct',
  ];

  protected $validationMessages = [
    'brand_name' => [
      'required' => 'El campo Nombre es obligatorio.',
      'is_unique' => 'El campo Nombre no puede repetirse.',
      'alpha_numeric_punct' => 'El campo Nombre solo admite alfanumérico, espacio o este conjunto limitado de
      caracteres de puntuación: 
      ~ (tilde),
      ! (exclamación), 
      # (número), 
      $ (dólar),
      % (porcentaje), 
      & (ampersand), 
      * (asterisco),
      - (guión), 
      _ (guión bajo), 
      + (más),
      = (igual), 
      | (barra vertical), 
      : (dos puntos),
      . (período).',
    ],
  ];

}
