<?php

namespace App\Services\Api;


class Api
{
    public function success($data, $status = 200)
    {
        return response()->json(['data' => $data, 'status' =>  $status, 'success' => true], $status);
    }

    public function failed($message = '', $data = [], $status = 200)
    {
        return response()->json(['message' => $message, 'data' => $data, 'success' => false],  $status);
    }
}
