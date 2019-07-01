<?php


namespace App\Http\Service;

use App\Models\User;
use App\Models\UserOauth;
use EasyWeChat\Factory;

class UserService
{

    protected $user;
    /**
     * UserService constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     * @param $code
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function  code2session($code)
    {

        $miniProgram = Factory::miniProgram(config('weChat.miniConfig'));

        $session = $miniProgram->auth->session($code);

        return $session;

    }

    public function findUserAndSendToken($request,$wxUser)
    {

        $openId = $wxUser['openid'];

        $oauth = UserOauth::where('oauth_id',$openId)->first();

        if (!count($oauth)){

            $user = $this->save($request,$wxUser);

        }else{

            $user = $oauth->user;
        }

        $userToken = $user->createToken($openId);

        $expireAt = now()->addDays(config('passport.tokensExpireIn'));

        $userToken->token->expires_at = $expireAt;

        $userToken->token->save();

        $token = [
            'access_token' => $userToken->accessToken,
            'expires_in' => $expireAt->toDateTimeString(),
            'token_type' => 'Bearer',
        ];

        return  $token;
    }

    public function save($request,$wxUser)
    {
        $user = $this->user;
        $user->name =  $request->nickname;
        $user->avatar = $request->avatar;
        $user->gender = $request->gender == '1' ?: '2';
        $user->password = $wxUser['session_key'];
        $user->save();
        //地址这里还要再看看逻辑
        $user->addresses()->create([
            'country' => $request->country?:'',
            'province' => $request->province?:'',
            'city' => $request->city?:'',
        ]);
        $user->oauths()->create([
            'nickname' => $request->nickname,
            'oauth_id' => $wxUser['openid'],
            'oauth_type' => 'weChat',
            'avatar' => str_replace('/132', '/0', $request->avatar),
        ]);

        return $user;
    }

}
