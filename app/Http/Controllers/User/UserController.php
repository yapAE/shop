<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Http\Service\UserService;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    //
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function wxLogin(Request $request)
    {

        $code = $request->code;

        $wxUser = $this->userService->code2session($code);

        if (isset($wxUser['errcode'])){

            return  $this->failed('code已过期或不正确');
        }

        $userToken = $this->userService->findUserAndSendToken($request,$wxUser);

        return $this->success($userToken);

    }
}
