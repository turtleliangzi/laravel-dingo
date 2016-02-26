<?php

namespace App\Api\V1\Controllers\Auth;

use App\Api\V1\Controllers\BaseController;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;

class AuthController extends BaseController {

    /**
     * @SWG\Post(
     *   path="/auth/login",
     *   summary="用户登录",
     *   tags={"Auth"},
     *   @SWG\Response(
     *     response=200,
     *     description="token"
     *   ),
     *   @SWG\Parameter(name="email", in="query", required=true, type="string", description="登录邮箱"),
     *   @SWG\Parameter(name="password", in="query", required=true, type="string", description="登录密码"),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */

    public function authenticate(Request $request) {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }
    /**
     * @SWG\Post(
     *   path="/auth/register",
     *   summary="用户注册",
     *   tags={"Auth"},
     *   @SWG\Response(
     *     response=200,
     *     description="register success"
     *   ),
     *   @SWG\Parameter(name="name", in="query", required=true, type="string", description="用户名"),
     *   @SWG\Parameter(name="email", in="query", required=true, type="string", description="登录邮箱"),
     *   @SWG\Parameter(name="password", in="query", required=true, type="string", description="登录密码"),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function register(Request $request) {

        $newUser = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
        ];
        $user = User::create($newUser);
        $token = JWTAuth::fromUser($user);

        return response()->json(compact('token'));
    }
}
