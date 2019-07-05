<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAddressesController extends ApiController
{

    /**
     * @param UserAddressRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserAddressRequest $request)
    {
        $user = Auth::user();
        $data = $user->addresses()->create($request->only([
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

    /**
     * @param UserAddress $userAddress
     * @param UserAddressRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UserAddress $userAddress,UserAddressRequest $request)
    {
        $this->authorize('own',$userAddress);
        $data = $userAddress->update($request->only([
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

    /**
     * @param UserAddress $userAddress
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destory(UserAddress $userAddress)
    {
        $this->authorize('own',$userAddress);

        $userAddress->delete();

        return  $this->message('您选择收货地址已删除');
    }

}
