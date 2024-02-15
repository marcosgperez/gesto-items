<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Auth\AuthResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class AuthController extends Controller
{
    use AuthResponse;
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }
    public function login(Request $request)
    {
        $validated = $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        if (!$token = auth()->attempt($validated)) {
            return $this->resultError('Wrong email or password', 'Wrong email or password', null, 401);
        }
        $response = [
            "token" => $token,
            "user" => auth()->user()
        ];
        // return $this->respondWithCookie()->json($response);
        return response()->json($response);
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            $auth = auth()->user();
            $userController = new UsersController();
            $userData = $userController->getCompanyAndType($auth->id);
        } catch (\Exception $error) {
            return response()->json($error->getMessage(), 500);
        }
        return response()->json($userData);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}