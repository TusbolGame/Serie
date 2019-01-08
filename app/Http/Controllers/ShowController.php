<?php

namespace App\Http\Controllers;

use App\ApiUpdate;
use App\Http\Controllers\Helpers\ShowHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ShowController extends Controller {
    private $updateLimitDays = 1;

    public function updateController($mainOptions) {
        // TODO Add event to notify the number of shows to be upodated
        if ($mainOptions['type'] == 0) {
            $showList = Show::all();
            $options = [];
        }
        if ($mainOptions['type'] == 1) {
            $limitDate = new Carbon($this->updateLimitDays . ' days ago midnight');
            $showList = Show::with('status')->where('updated_at', '<', $limitDate->getTimestamp());
            $options = [];
        } else if ($mainOptions['type'] == 2) {
            $showList = Show::where('uuid', $mainOptions['stream'])->get();
            $options = [];
        } else {
            // TODO Handle error on wrong update type
        }
        foreach ($showList as $show) {
            $this->updateHandler($show, $options);
        }
    }

    public function updateHandler($show, $options = []) {
        if ($show->status->name == 'Ended') {
            $rawData = fetchData($show->api_id, 'minimum');
        } else {
            $rawData = fetchData($show->api_id, 'everything');
        }
        if ($rawData == 0) {
            return -1;
        }
        if ($rawData == 1) {
            return -2;
        }

        $apiUpdateCheck = ApiUpdate::where([
            'show_id' => $show->id,
            'api_updated_at' => Carbon::createFromTimestamp($rawData->updated)->toDateTimeString(),
        ])->first();

        if ($apiUpdateCheck != NULL) {
            return 0;   // No update needed
        }
        $showHelper = new ShowHelper();
        $showResult = $showHelper->updateData();
    }
}
