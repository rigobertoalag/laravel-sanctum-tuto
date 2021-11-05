<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class ChecksController extends Controller
{
    public function statusToken(){
        $current_user = Auth::user();

        if(!$current_user){
            return response([
                'error' => 'token invalido',
            ],404);
        }
        return response([
            'message' => 'token valido',
            'currentUser' => $current_user
        ],200);
    }

    public function statuscheck()
    {
        $current_user_id = Auth::user()->id;

        $day = Carbon::today()->format("Y-m-d H:m:s");

        $lastCheckin = DB::table('check_ins')->where([
            ['user_id', '=', $current_user_id],
            ['created_at', '>=', $day]
        ])->latest('id')->first();

        $lastCheckOut = DB::table('check_outs')->where([
            ['user_id', '=', $current_user_id],
            ['created_at', '>=', $day]
        ])->latest('id')->first();

        if(!$lastCheckin && !$lastCheckOut){
            return response([
                'message' => 'Turno del dia no iniciado',
                'beginTurn' => false,
                'ins' => $lastCheckin,
                'outs' => $lastCheckOut,
            ], 200);
        }

        if($lastCheckin && !$lastCheckOut){
            return response([
                'message' => 'Turno iniciado pero no terminado',
                'beginTurn' => true,
                'ins' => $lastCheckin,
                'outs' => $lastCheckOut,
            ], 200);
        }

        if($lastCheckin && $lastCheckOut){

            $li = $lastCheckin->created_at;
            $lo = $lastCheckOut->created_at;

            $newCheckin = DB::table('check_ins')->where([
                ['user_id', '=', $current_user_id],
                ['created_at', '>', $lo]
            ])->latest('id')->first();
    
            $newCheckOut = DB::table('check_outs')->where([
                ['user_id', '=', $current_user_id],
                ['created_at', '>', $li]
            ])->latest('id')->first();

            if($newCheckin && !$newCheckOut){
                return response([
                    'message' => 'Turno nuevo iniciado, pero no finalizado',
                    'beginTurn' => true,
                    'ins' => $newCheckin,
                    'outs' => $newCheckOut,
                ], 200);
            }

            $checkFull = DB::table('check_fulls')->where([
                ['id_check_in', '=', $lastCheckin->id],
                ['id_check_out', '=', $lastCheckOut->id]
            ])->latest('id')->first();

            if(!$checkFull){
                DB::table('check_fulls')->insert([
                    'user_id' => $current_user_id,
                    'id_check_in' => $lastCheckin->id,
                    'id_check_out' => $lastCheckOut->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            return response([
                'message' => 'Turno completado',
                'beginTurn' => false,
                'ins' => $lastCheckin,
                'outs' => $lastCheckOut,
                'full' => $checkFull
            ], 200);
        }

        return response([
            'error' => 'error'
        ], 404);
    }
}
