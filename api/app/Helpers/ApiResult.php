<?php

namespace App\Helpers;

class ApiResult
{
    public bool $status;
    public mixed $data;
    public string $message;

    public function __construct(bool $status, mixed $data, string $message) {
        $this->status = $status;
        $this->data = $data;
        $this->message = $message;
    }
}
