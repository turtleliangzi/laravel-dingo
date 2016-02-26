<?php

namespace App\Api\V1\Controllers\User;

use App\Api\V1\Controllers\BaseController;
use App\User;
use Swagger\Annotations as SWG;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Api\V1\Transformers\UserTransformer;

/**
 * @SWG\Swagger(
 *   @SWG\Info(
 *     title="Laravel-dingo",
 *     version="1.0.0"
 *   ),
 *   @SWG\Tag(name="Auth", description="验证模块"),
 *   @SWG\Tag(name="Users", description="用户模块"),
 *   @SWG\Tag(name="Lessons", description="教程模块"),
 *   schemes={"http"},
 *   host="ucenter.turtletl.com:81",
 *   basePath="/api"
 * )
 */

class UserController extends BaseController {
    /**
     * @SWG\Get(
     *   path="/users/all",
     *   summary="显示所有用户",
     *   tags={"Users"},
     *   @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="all users"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */

    public function show() {
        $users =  User::all();
        $users = User::paginate(25);
        return $this->response->paginator($users, new UserTransformer)->setStatusCode(200);
        //return $this->response->collection($users, new UserTransformer);
    }
    /**
     * @SWG\Get(
     *   path="/users/one",
     *   summary="获取当前用户",
     *   tags={"Users"},
     *   @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="one user"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
        //return $this->response->item(compact('user'), new UserTransformer);
    }
}
