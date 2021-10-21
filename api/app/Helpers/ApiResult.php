<?php

namespace App\Helpers;

class ApiResult
{
    public bool $status;
    public mixed $data;
    public string $message;
    public mixed $errors;

    public function __construct(bool $status, mixed $data, string $message, mixed $errors = null) {
        $this->status = $status;
        $this->data = $data;
        $this->message = $message;
        $this->errors = $errors;
    }
}
