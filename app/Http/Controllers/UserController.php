<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response([
                'message' => ['Usuario no encnotrado']
            ], 404);
        } else if ($user) {
            $hashed_pass = Hash::check($request->password, $user->password);
            if (!$hashed_pass) {
                return response([
                    'message' => ['Contraseña erronea']
                ], 404);
            }

            $token = $user->createToken('userToken')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

            return response($response, 201);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validate(),
            ['password' => Hash::make($request->password)]
        ));

        return response()->json([
            'message' => 'Usuario registrado',
            'user' => $user
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response([
            'message' => 'salio'
        ], 200);
    }

    public function test()
    {
        return response([
            'message' => 'hola desde test'
        ], 200);
    }
}
