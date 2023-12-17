<?php

namespace App\Http\Controllers\Api;

use DB;
use Exception;

use App\Models\User;
use App\Models\Karyawan;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function store(Request $request) {
        $responseOutput = $this->responseOutput;

        $input = $request->all();

        $password = $input['password'] == 'generate' ? Str::random(8) : $input['password'];
        $password_hash = Hash::make($password);

        DB::beginTransaction();
        try {
            $uuid = (string) Str::uuid();

            Karyawan::insert([
                'id'            => $uuid,
                'perusahaan_id' => $input['perusahaan_id'],
                'identify_id'   => $input['identify_id'],
                'type_id'       => $input['type_id'],
                'position'      => $input['position'],
                'first_name'    => $input['first_name'],
                'last_name'     => $input['last_name'],
                'phone_number'  => $input['phone_number'],
                'birthdate'     => $input['birthdate'],
                'gender'        => $input['gender'],
                'address'       => $input['address'],
                'salary'        => $input['salary'],
                'created_at'    => now()->timestamp,
            ]);

            User::insert([
                'id'            => $uuid,
                'email'         => $input['email'],
                'password'      => $password_hash,
                'created_at'    => now()->timestamp,
            ]);

            if(Auth::attempt(['email' => $input['email'], 'password' => $password])) {
                $user = Auth::user();
                $token = $user->createToken('PresensiToken', ['karyawan:outsource'])->plainTextToken;
            }

            DB::commit();

            $responseOutput['success'] = true;
            $responseOutput['message'] = 'Success';
            $responseOutput['data'] = [
                'user' => $user,
                'token' => $token,
            ];

            return response()->json($responseOutput);
        } catch(Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }
}
