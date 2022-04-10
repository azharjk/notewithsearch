<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Http\Resources\TokenResource;
use App\Constants\TokenConstant;

class RegisterController extends Controller
{
    public function index(Request $request)
    {
        $validator = $this->__validate($request->all(), [
            'name' => 'required',
            'username' => 'required|unique:users|min:8',
            'password' => 'required|min:8'
        ]);

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
