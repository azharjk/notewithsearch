<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class LogoutController extends Controller
{
    public function index(Request $request)
    {
        $request->user()->tokens()->delete();

        $response = Response::json(['message' => 'Logged out']);

        return $response;
    }
}
