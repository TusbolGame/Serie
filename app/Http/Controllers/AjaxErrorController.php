<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class AjaxErrorController extends Controller {
    private $message;
    private $status;
    private $data;

    public function __construct($message, $status, $data = NULL) {
        $this->message = $message;
        $this->status = $status;
        $this->data = $data;
    }

    public function __toString() {
        return json_encode([
            'message' => $this->message,
            'status' => $this->status,
            'data' => $this->data,
        ]);
    }
}

