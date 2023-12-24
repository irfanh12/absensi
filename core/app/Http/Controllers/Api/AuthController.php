<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    /**
     * Login function.
     *
     * @param Request $request The request object.
     * @throws None
     * @return JsonResponse The JSON response.
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

        if (Auth::attempt(request(['email','password']))) {
            $user = $request->user();

            //user details
            $user_details = [];
            $user_details['email'] = $user->email;
            $user_details['karyawan'] = $user->karyawan;
            $user_details['karyawan']['initial'] = $user->karyawan->initialName;
            $user_details['id'] = $user->id;

            //output
            $responseOutput['success'] = true;
            $responseOutput['message'] = 'Logged in.';

            $user->tokens()->delete();

            $user_type = $user->karyawan->user_type;
            $user_type = Str::snake($user_type->type);

            $token = $user->createToken('PresensiToken', $this->enumType($user_type))->plainTextToken;
            $responseOutput['data'] = [
                'token' => $token,
                'user_details' => $user_details,
            ];

            return response()->json($responseOutput);
        } else {
            return response()->json($responseOutput, 401);
        }
    }

    public function profile(Request $request)
    {
        $responseOutput = $this->responseOutput;

        $user = Auth::user();
        if($user) {
            $responseOutput['success'] = true;
            $responseOutput['message'] = 'Success';
        }

        return response()->json($responseOutput);
    }
}
