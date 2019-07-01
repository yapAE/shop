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

    /**
     * @param $request
     * @param $wxUser
     * @return array
     */
    public function findUserAndSendToken($request,$wxUser)
    {

        $openId = $wxUser['openid'];

        $oauth = UserOauth::where('oauth_id',$openId)->first();

        if (!count($oauth)){
            //此处判断还需优化

            $user = $this->save($request,$wxUser);

        }else{

            $user = $oauth->user;
            //更新用户信息
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

    /**
     * @param $request
     * @param $wxUser
     * @return User
     */
    public function save($request,$wxUser)
    {
        $nickname = $request->nickname;
        $avatar = $request->avatar;
        $user = $this->user;
        $user->name =  $nickname;
        $user->password = $wxUser['session_key'];
        $user->save();

        $user->profile()->create([
            'nickname' => $nickname,
            'avatar' => $avatar,
            'gender' => $request->gender == '1' ?: '2',
            'country' => $request->country?:'',
            'province' => $request->province?:'',
            'city' => $request->city?:'',
        ]);

        $user->oauths()->create([
            'nickname' => $nickname,
            'oauth_id' => $wxUser['openid'],
            'oauth_type' => 'weChat',
            'avatar' => str_replace('/132', '/0', $avatar),
        ]);

        return $user;
    }

}
