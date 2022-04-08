<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Response;

class Authenticate extends Middleware
{
    protected function unauthenticated($request, array $guards)
    {
        $response = Response::json(['message' => 'Unauthorized'], 401);

        abort($response);
    }
}
