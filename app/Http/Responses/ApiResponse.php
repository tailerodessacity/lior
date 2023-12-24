<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    public function __construct($data = [], $status = JsonResponse::HTTP_OK, $headers = [], $options = 0)
    {
        parent::__construct($data, $status, $headers, $options);
    }
}
