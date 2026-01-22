<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validateData = $request->validated();
        $user = User::create([
            'name'     => $validateData['name'],
            'email'    => $validateData['email'],
            'password' => Hash::make($validateData['password']),
        ]);
        $token = $user->createToken('auth_token');

        return response()->json([
            "success" => true,
            "message" => "User registered successfully",
            'user'         => $user,
            'access_token' => $token,
        ], 201);
    }
    public function login(LoginRequest $request)
    {
        $validateData = $request->validated();
        if (!Auth::guard('web')->attempt($validateData)) {
            throw ValidationException::withMessages([
                'email' => ['The email and password do not match'],
            ]);
        }
        $user = Auth::user();
        return response()->json([
            "success" => true,
            "message" => "User login successfully",
            $user
        ]);
    }
}


// 1|eMppLuvmdNzwE6TtsjywiTO2NZw2ryDlqbxesHrGc303c8d0
