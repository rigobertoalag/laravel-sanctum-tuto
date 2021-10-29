<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CheckOutRequest extends FormRequest{

    public function authorize(){
        return Auth::check();
    }

    public function rules(){
        return[
            'location' => 'required',
            'image' => 'required|image|max:1024'
        ];
    }
}