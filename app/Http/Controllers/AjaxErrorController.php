<?php

namespace App\Http\Controllers;

class AjaxErrorController extends Controller {
    private $message;
    private $status;
    private $data;

    public static function response($message, $status, $data = NULL) {
        $javascriptError = new JavascriptErrorController();
        $javascriptError->errorHandler(NULL, $data, $status);

        return response()->json([
            'message' => $message,
            'status' => $status,
            'data' => $data,
            ], $status);
    }
}

