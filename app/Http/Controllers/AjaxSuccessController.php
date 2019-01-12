<?php


namespace App\Http\Controllers;

class AjaxSuccessController extends Controller {
    private $message;
    private $data;

    public function __construct($message, $data = NULL) {
        $this->message = $message;
        $this->data = $data;
    }

    public function __toString() {
        return json_encode([
            'message' => $this->message,
            'data' => $this->data,
        ]);
    }
}
