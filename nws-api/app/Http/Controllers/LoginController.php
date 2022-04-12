<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Http\Resources\TokenResource;
use App\Constants\TokenConstant;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        $validator = $this->__validate($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        $validated = $validator->validated();

        $user = User::where('username', $validated['username'])->first();

        if (! $user) {
            return Response::json(['message' => 'Username is incorrect'], 401);
        }

        if (! Hash::check($validated['password'], $user->password)) {
            return Response::json(['message' => 'Password is incorrect'], 401);
        }

        $token = $user->createToken(TokenConstant::AUTH_TOKEN_NAME);

        return new TokenResource($token);
    }
}
