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
                $response = array(
                    'error' => '用户名或密码错误',
                    'status' => 401
                );
                return response()->json($response);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            $response = array(
                'error' => '创建token时出错',
                'status' => 500
            );
            return response()->json($response);
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
        $userExist = User::findUserEmail($newUser['email']);
        if (!empty($userExist)) {
            $response = array(
                'error' => '该邮箱已注册',
                'status' => 400
            );
            return response()->json($response);
        }
        $user = User::create($newUser);
        $token = JWTAuth::fromUser($user);

        return response()->json(compact('token'));
    }
    /**
     * @SWG\Post(
     *   path="/auth/resetPassword",
     *   summary="重置密码",
     *   tags={"Auth"},
     *   @SWG\Response(
     *     response=200,
     *     description="modify success"
     *   ),
     *   @SWG\Parameter(name="email", in="query", required=true, type="string", description="登录邮箱"),
     *   @SWG\Parameter(name="password", in="query", required=true, type="string", description="登录密码"),
     *   @SWG\Parameter(name="resetPassword", in="query", required=true, type="string", description="确认密码"),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function resetPassword(Request $request){
        $per = [
           'email'=>$request ->get('email'),
           'password'=>bcrypt($request ->get('password')),
       ];
        $peo = [
           'resetPassword'=>bcrypt($request ->get('resetPassword'))
        ];
        $userExist = User::findUserEmail($per['email']);
        if(empty($userExist)){
            $response = array(
                'error'=>'用户不存在',
                'status'=>400,
                );
            return response() -> json($response);
        }
        $user = User::changePassword($userExist['id'],$per['password']);
        if($user === false){
            return $this->errorResponse("重置密码失败");
        } else {
            return $this->successResponse("重置密码成功");
        }

    }
}
