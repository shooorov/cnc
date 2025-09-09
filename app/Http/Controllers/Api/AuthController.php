<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function auth(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $params = [
                'user' => $user,
            ];

            return response()->json($params, 200);
        }

        return response()->json(null, 400);
    }

    public function users(Request $request)
    {
        dd('here');
    }
}
