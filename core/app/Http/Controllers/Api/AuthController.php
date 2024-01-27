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
        $responseOutput['message'] = trans('response.error.login_failed');

        // validate incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            // $responseOutput['message'] = $validator->messages()->first();
            abort(403, $responseOutput['message']);
        }

        if (Auth::attempt(request(['email','password']))) {
            $user = $request->user();

            //user details
            $user_details = [
                'id' => $user->karyawan->id,
                'fullname' => $user->karyawan->fullname,
                'position' => $user->karyawan->position,
                'email' => $user->email,
                'phone_number' => $user->karyawan->phone_number,
                'user_type' => $user->karyawan->user_type,
                'perusahaan' => $user->karyawan->perusahaan,
                'karyawan' => [
                    'first_name' => $user->karyawan->first_name,
                    'last_name' => $user->karyawan->last_name,
                ]
            ];
            //output
            $responseOutput['success'] = true;
            $responseOutput['message'] = trans('response.success.auth_success');

            // $user->tokens()->delete();

            $user_type = $user->karyawan->user_type;
            $position = $user_type->type;

            $token = $user->createToken('PresensiToken', $this->enumType(Str::snake($position)))->plainTextToken;
            $responseOutput['data'] = [
                'token' => $token,
                'position' => $position,
                'user_details' => $user_details,
            ];

            return response()->json($responseOutput);
        } else {
            abort(401, $responseOutput['message']);
        }
    }

    public function profile(Request $request)
    {
        $responseOutput = $this->responseOutput;

        $user = Auth::user();
        if($user) {
            $responseOutput['success'] = true;
            $responseOutput['message'] = trans('response.success.auth_success');
        }

        return response()->json($responseOutput);
    }
}
