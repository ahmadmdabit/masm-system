<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResult;
use Illuminate\Support\Facades\Request;

class AuthController extends Controller
{
    /**
     * Logout the user.
     *
     * @param  Request $request
     * @return Response
     */
    public function logout(Request $request)
    {
        try {
            auth()->user()->tokens()->each(function($token) {
                $token->delete();
            });
            $result = new ApiResult(false, null, 'The user logged out successfully.');
        } catch (\Throwable $e) {
            $result = new ApiResult(false, null, $e->getMessage());
        }
        return response()->json($result);
    }
}
