<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function me()
    {
        return success(auth()->user());
    }
}
