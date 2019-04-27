<?php

namespace App\Http\Controllers;

use App\JavascriptError;
use Illuminate\Http\Request;

class JavascriptErrorController extends Controller {
    public function errorManager() {
        $url = NULL;
        $data = NULL;
        $error = NULL;
        if (is_array(request()->error)) {
            if (isset(request()->error['url'])) {
                $url = request()->error['url'];
            }
            if (isset(request()->error['data'])) {
                $data = request()->error['data'];
            }
            if (isset(request()->error['error'])) {
                $error = request()->error['error'];
            }
        } else {
            $error = request()->error;
        }

        return $this->errorHandler($url, $data, $error);
    }

    public function errorHandler($url, $data, $error) {
        $javascriptError = new JavascriptError([
            'url' => $url,
            'data' => $data,
            'error' => $error,
        ]);

        $javascriptError->save();

        return new AjaxSuccessController("Error add Successful", []);
    }
}
