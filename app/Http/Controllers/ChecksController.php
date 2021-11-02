<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class ChecksController extends Controller
{
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
                'ins' => $lastCheckin,
                'outs' => $lastCheckOut,
            ], 200);
        }

        if($lastCheckin && !$lastCheckOut){
            return response([
                'message' => 'Turno iniciado pero no terminado',
                'ins' => $lastCheckin,
                'outs' => $lastCheckOut,
            ], 200);
        }

        if($lastCheckin && $lastCheckOut){

            $li = $lastCheckin->created_at;
            $lo = $lastCheckOut->created_at;

            $lci = Carbon::parse($lastCheckin->created_at)->format("Y-m-d H:m:s");
            $lco = Carbon::parse($lastCheckOut->created_at)->format("Y-m-d H:m:s");

            $newCheckin = DB::table('check_ins')->where([
                ['user_id', '=', $current_user_id],
                ['created_at', '>', $lci]
            ])->latest('id')->first();
    
            $newCheckOut = DB::table('check_outs')->where([
                ['user_id', '=', $current_user_id],
                ['created_at', '>', $li]
            ])->latest('id')->first();

            return response([
                'message' => 'Turno completado',
                'lastCheckin' => $li,
                'lastCheckOut' => $lo,
                'lci' => $lci,
                'lco' => $lco,
                'ins' => $newCheckin,
                'outs' => $newCheckOut,
            ], 200);
        }

        return response([
            'error' => 'error'
        ], 200);
    }
}
