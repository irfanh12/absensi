<?php

namespace App\Http\Controllers\Api;

use Exception;

use App\Models\User;
use App\Models\Karyawan;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function lists(Request $request) {
        $responseOutput = $this->responseOutput;

        $input = $request->all();

        $lists = Karyawan::where("type_id", $input["type_id"] ?? 4)->paginate($input['per_page'] ?? 10);

        $responseOutput['success'] = true;
        $responseOutput['message'] = trans('response.success.get_karyawan');
        $responseOutput['data'] = $lists;

        return response()->json($responseOutput);
    }

    /**
     * Store the data from the request.
     *
     * @param Request $request The request object containing the data to be stored.
     * @throws Exception If an error occurs during the database transaction.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the stored data.
     */
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

            DB::commit();

            $user = User::find($uuid);
            // $user->tokens()->delete();

            // $user_type = $user->karyawan->user_type;
            // $user_type = Str::snake($user_type->type);

            // $token = $user->createToken('PresensiToken', $this->enumType($user_type))->plainTextToken;

            $responseOutput['success'] = true;
            $responseOutput['message'] = trans('response.success.post_karyawan');
            $responseOutput['data'] = [
                'user' => $user,
            ];

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function update(Request $request, $uuid) {
        $responseOutput = $this->responseOutput;

        $input = $request->all();

        $password = $input['password'] == 'generate' ? Str::random(8) : $input['password'];
        $password_hash = Hash::make($password);

        DB::beginTransaction();
        try {
            Karyawan::where([
                ['id', $uuid]
            ])->update([
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
                'updated_at'    => now()->timestamp,
            ]);

            User::where([
                ['id', $uuid]
            ])->update([
                'email'         => $input['email'],
                'password'      => $password_hash,
                'updated_at'    => now()->timestamp,
            ]);

            DB::commit();

            $user = User::find($uuid);
            // $user->tokens()->delete();

            // $user_type = $user->karyawan->user_type;
            // $user_type = Str::snake($user_type->type);

            // $token = $user->createToken('PresensiToken', $this->enumType($user_type))->plainTextToken;

            $responseOutput['success'] = true;
            $responseOutput['message'] = trans('response.success.post_karyawan');
            $responseOutput['data'] = [
                'user' => $user,
            ];

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function delete(Request $request, $uuid) {
        $responseOutput = $this->responseOutput;

        $input = $request->all();

        $password = $input['password'] == 'generate' ? Str::random(8) : $input['password'];
        $password_hash = Hash::make($password);

        DB::beginTransaction();
        try {
            Karyawan::where([
                ['id', $uuid]
            ])->update([
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
                'updated_at'    => now()->timestamp,
            ]);

            User::where([
                ['id', $uuid]
            ])->update([
                'email'         => $input['email'],
                'password'      => $password_hash,
                'updated_at'    => now()->timestamp,
            ]);

            DB::commit();

            $user = User::find($uuid);
            // $user->tokens()->delete();

            // $user_type = $user->karyawan->user_type;
            // $user_type = Str::snake($user_type->type);

            // $token = $user->createToken('PresensiToken', $this->enumType($user_type))->plainTextToken;

            $responseOutput['success'] = true;
            $responseOutput['message'] = trans('response.success.post_karyawan');
            $responseOutput['data'] = [
                'user' => $user,
            ];

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }
}
