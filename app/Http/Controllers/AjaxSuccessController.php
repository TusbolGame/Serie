<?php


namespace App\Http\Controllers;

class AjaxSuccessController extends Controller {
    private $message;
    private $data;

    public static function response($message, $data = NULL) {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], 200);
    }
}
