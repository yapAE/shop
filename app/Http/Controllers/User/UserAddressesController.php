<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Http\Requests\UserAddressRequest;
use Illuminate\Http\Request;

class UserAddressesController extends ApiController
{


    public function store(UserAddressRequest $request)
    {

        $data = $request->user()->addresses()->create($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
        ]));
        return  $this->success($data);
    }
}
