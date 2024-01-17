<?php

namespace App\Http\Controllers\Api;

use Exception;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function lists(Request $request) {
        $responseOutput = $this->responseOutput;

        $input = $request->all();

        $lists = User::whereHas('karyawan', function ($query) use ($input) {
            $query->where("type_id", $input["type_id"] ?? 4);
        })->paginate($input['per_page'] ?? 10);

        $responseOutput['success'] = true;
        $responseOutput['message'] = trans('response.success.get_klien');
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

        $klien = $input['klien'];
        $perusahaan = $input['perusahaan'];

        $password = $klien['password'] == 'generate' ? Str::random(8) : $klien['password'];
        $password_hash = Hash::make($password);

        DB::beginTransaction();
        try {
            $perusahaan_id = (string) Str::uuid();

            $perusahaan['id'] = $perusahaan_id;
            $perusahaan['identify_id'] = $klien['identify_id'];
            $perusahaan['created_at'] = now()->timestamp;
            Perusahaan::insert($perusahaan);

            $uuid = (string) Str::uuid();
            $klien['perusahaan_id'] = $perusahaan_id;
            Karyawan::insert([
                'id'            => $uuid,
                'perusahaan_id' => $klien['perusahaan_id'],
                'identify_id'   => $klien['identify_id'],
                'type_id'       => $klien['type_id'],
                'position'      => $klien['position'],
                'first_name'    => $klien['first_name'],
                'last_name'     => $klien['last_name'],
                'phone_number'  => $klien['phone_number'],
                'birthdate'     => $klien['birthdate'],
                'gender'        => $klien['gender'],
                'address'       => $klien['address'],
                'salary'        => $klien['salary'],
                'created_at'    => now()->timestamp,
            ]);

            User::insert([
                'id'            => $uuid,
                'email'         => $klien['email'],
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
            $responseOutput['message'] = trans('response.success.post_klien');
            $responseOutput['data'] = [
                'user' => $user,
            ];

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function edit(Request $request, $id) {
        $responseOutput = $this->responseOutput;

        try {
            $responseOutput['success'] = true;
            $responseOutput['message'] = trans('response.success.load_klien');
            $responseOutput['data'] = User::find($id);

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    public function update(Request $request, $id) {
        $responseOutput = $this->responseOutput;

        $input = $request->all();

        $klien = $input['klien'];
        $perusahaan = $input['perusahaan'];

        $password = $klien['password'] == 'generate' ? Str::random(8) : $klien['password'];
        $password_hash = Hash::make($password);

        DB::beginTransaction();
        try {
            $data = User::find($id);
            $perusahaan_id = $data->karyawan->perusahaan_id;

            $perusahaan['identify_id'] = $klien['identify_id'];
            $perusahaan['updated_at'] = now()->timestamp;
            Perusahaan::where('id', $perusahaan_id)->update($perusahaan);

            $uuid = $data->id;
            $klien['perusahaan_id'] = $perusahaan_id;
            Karyawan::where('id', $uuid)->update([
                'perusahaan_id' => $klien['perusahaan_id'],
                'identify_id'   => $klien['identify_id'],
                'type_id'       => $klien['type_id'],
                'position'      => $klien['position'],
                'first_name'    => $klien['first_name'],
                'last_name'     => $klien['last_name'],
                'phone_number'  => $klien['phone_number'],
                'birthdate'     => $klien['birthdate'],
                'gender'        => $klien['gender'],
                'address'       => $klien['address'],
                'salary'        => $klien['salary'],
                'updated_at'    => now()->timestamp,
            ]);

            User::where('id', $uuid)->update([
                'email'         => $klien['email'],
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
            $responseOutput['message'] = trans('response.success.put_klien');
            $responseOutput['data'] = [
                'user' => $user,
            ];

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function destroy(Request $request, $uuid) {
        $responseOutput = $this->responseOutput;

        $input = $request->all();

        DB::beginTransaction();
        try {

            Karyawan::find($uuid)->delete();
            Perusahaan::find($input['perusahaan_id'])->delete();

            DB::commit();

            $responseOutput['success'] = true;
            $responseOutput['message'] = trans('response.success.delete_klien');

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }
}
