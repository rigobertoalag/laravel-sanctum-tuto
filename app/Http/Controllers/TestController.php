<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function test2(){
        return response([
            'message' => 'desde test2'
        ], 200);
    }

    public function userdatatest(){
        $userData = auth()->user();

        if(!$userData){
            response([
                'error' => 'No autorizado'
            ], 401);
        }

        return response([
            'userData' => [$userData->name]
        ],200);
    }
}
