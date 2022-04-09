<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Http\Resources\TokenResource;
use App\Constants\TokenConstant;

class RegisterController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|unique:users|min:8',
            'password' => 'required|min:8'
        ]);


        if ($validator->fails()) {
            return Response::make($validator->errors(), 400);
        }

        $validated = $validator->validated();

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password'])
        ]);

        $token = $user->createToken(TokenConstant::AUTH_TOKEN_NAME);

        return new TokenResource($token);
    }
}
