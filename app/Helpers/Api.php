<?php

use App\Services\Api\Api;

function success($data, $status = 200)
{
    return (new Api())->success($data, $status);
}


function failed($message, $data = [], $status = 200)
{
    return (new Api())->failed($message , $data, $status);
}
