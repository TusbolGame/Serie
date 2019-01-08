<?php

namespace App\Http\Controllers;

use App\Network;
use Illuminate\Http\Request;

class NetworkController extends Controller {
    public function createUpdate($data, $networkType) {
        $network = Network::updateOrCreate(
            [
                'name' => ($data->name != NULL) ? $data->name : 'No Network Available'
            ],
            [
                'name' => ($data->name != NULL) ? $data->name : 'No Network Available',
                'country_code' => ($data->country != NULL) ? $data->country->code : NULL,
                'country_name' => ($data->country != NULL) ? $data->country->name : NULL,
                'type' => $networkType,
            ]);

        return $network;
    }

    public function returnDefaultNetwork() {
        return Network::where(['name' => 'No Network Available'])->first();
    }
}
