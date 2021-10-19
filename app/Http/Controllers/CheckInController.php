<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use Illuminate\Http\Request;

use App\Http\Requests\CheckInRequest;
use Illuminate\Support\Facades\Auth;

class CheckInController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current_user_id = Auth::user()->id;
        return response([
            'message' => CheckIn::all()->where('user_id', $current_user_id)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CheckInRequest $request)
    {
        $request->validated();

        $user = Auth::user();

        $checkin = new CheckIn();
        $checkin->user()->associate($user);
        $url_image = $this->upload($request->file('image'));
        $checkin->image = $url_image;
        $checkin->location = $request->input('location');

        $res = $checkin->save();

        if($res){
            return response([
                'message' => 'Se guardo correctamente'
            ], 201);
        }
        return response([
            'message' => 'Error'
        ], 500);
    }

    public function upload($image){
        $path_info = pathinfo($image->getClientOriginalName());
        $post_path = 'images/checkin';

        $rename = uniqid() . '.' . $path_info['extension'];
        $image->move(public_path() . "/$post_path",$rename);

        return "$post_path/$rename";
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CheckIn  $checkIn
     * @return \Illuminate\Http\Response
     */
    public function show(CheckIn $checkIn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CheckIn  $checkIn
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CheckIn $checkIn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CheckIn  $checkIn
     * @return \Illuminate\Http\Response
     */
    public function destroy(CheckIn $checkIn)
    {
        //
    }
}
