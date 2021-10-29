<?php

namespace App\Http\Controllers;

use App\Models\CheckOut;
use Illuminate\Http\Request;

use App\Http\Requests\CheckOutRequest;
use Illuminate\Support\Facades\Auth;

class CheckOutController extends Controller
{
    public function index(){
        $current_user_id = Auth::user()->id;

        return response([
            'message' => CheckOut::all()->where('user_id', $current_user_id)
        ]);
    }

    public function store(CheckOutRequest $request){
        $request->validated();

        $user = Auth::user();

        $checkout = new CheckOut();
        $checkout->user()->associate($user);
        $url_image = $this->upload($request->file('image'));
        $checkout->image = $url_image;
        $checkout->location = $request->input('location');

        $res = $checkout->save();

        if($res){
            return response([
                'message' => 'Se guardo correcto'
            ], 201);
        }
        return response([
            'message' => 'Error'
        ], 500);
    }

    public function upload($image){
        $path_info = pathinfo($image->getClientOriginalName());
        $post_path = 'public/storage';

        $rename = uniqid() . '.' . $path_info['extension'];
        $image->move(public_path() . "/$post_path",$rename);

        return "$post_path/$rename";
    }
}
