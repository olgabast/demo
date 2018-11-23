<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAuthRequest;
use App\User;
use Dingo\Api\Contract\Http\Request;
use Dingo\Api\Routing\Helpers;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    use Helpers;

    /**
     * Create new user, return JWT Auth token
     *
     * @param UserAuthRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function register(UserAuthRequest $request)
    {
        if (User::create($request->only('email', 'password'))) {
            return $this->login($request)->setStatusCode(201);
        }

        return $this->response->errorBadRequest();
    }

    /**
     * Log user in, return JWT Auth token
     *
     * @param UserAuthRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function login(UserAuthRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->response->errorUnauthorized('Bad credentials');
            }
        } catch (JWTException $e) {
            return $this->response->error('could_not_create_token', 500);
        }
        return response()->json(compact('token'));
    }

    public function user()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
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
        return response()->json($user);
    }

    public function token()
    {
        $token = JWTAuth::getToken();
        if (!$token) {
            return $this->response->errorMethodNotAllowed('Token not provided');
        }
        try {
            $refreshedToken = JWTAuth::refresh($token);
        } catch (JWTException $e) {
            return $this->response->errorInternal('Not able to refresh Token');
        }
        return $this->response->withArray(['token' => $refreshedToken]);
    }
}