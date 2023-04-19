<?php namespace App\Libraries;

use App\Models\ClientModel;
use App\Models\OfficeModel;

class OfficesLib
{
    /**
     * Get all recipes
     * @return array
     */
    public function getAll ()
    {
        // Create an instance for our two models
        $clientModel = new ClientModel();
        $officeModel = new OfficeModel();

        // SELECT the recipes, order by id
        $offices = $officeModel
            ->asObject()
            ->orderBy('office_name')
            ->findAll();

        // For each recipe, SELECT its ingredients
        foreach ($offices as &$office)
        {
            $client = $clientModel->find($office->client_id);
            $office->client_name = $client->client_name;
        }
        return $offices;
    }
}

