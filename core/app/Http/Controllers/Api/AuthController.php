<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Handle login
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $responseOutput = $this->responseOutput;
        $responseOutput['message'] = 'Login failed. Please check email and password.';

        // validate incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            // $responseOutput['message'] = $validator->messages()->first();
            return response()->json($responseOutput, 403);
        }

        //continue
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();

            //user details
            $user_details = [];
            $user_details['email'] = $user->email;
            $user_details['id'] = $user->id;

            //output
            $responseOutput['success'] = true;
            $responseOutput['message'] = 'Logged in.';

            $user->tokens()->delete();
            $responseOutput['data'] = [
                'token' => $user->createToken('PresensiToken', ['karyawan:outsource'])->plainTextToken,
                'user_details' => $user_details,
            ];

            return response()->json($responseOutput);
        } else {
            return response()->json($responseOutput, 401);
        }
    }
}
