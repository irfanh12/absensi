<?php

namespace App\Http\Controllers\Api;

use Exception;

use App\Models\User;
use App\Models\Karyawan;
use App\Http\Controllers\Controller;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate as CellCoordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function lists(Request $request) {
        $responseOutput = $this->responseOutput;

        $input = $request->all();

        $lists = User::whereHas('karyawan', function ($query) use ($input) {
            if(is_array($input["type_id"])) {
                $query->whereIn("type_id", $input["type_id"]);
            } else {
                $query->whereIn("type_id", $input["type_id"]);
            }
        })->paginate($input['per_page'] ?? 10);

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

        $validator = validator($input, [
            'identify_id' => 'required|unique:karyawan',
            'email' => 'required|email|unique:users',
        ]);
        if($validator->fails()) {
            abort(500, $validator->messages()->first());
        }

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

    public function update(Request $request, $uuid) {
        $responseOutput = $this->responseOutput;

        $data = User::find($uuid);
        $input = $request->all();

        $rules = [
            'identify_id' => [
                'required',
                Rule::unique('karyawan')->ignore($uuid)
            ],
            'email' => [
                'required',
                Rule::unique('users')->ignore($uuid)
            ],
        ];
        if (isset($input['identify_id']) && $input['identify_id'] !== $data->karyawan->identify_id) {
            $rules['identify_id'] = 'required|unique:karyawan';
        }

        if (isset($input['email']) && $input['email'] !== $data->email) {
            $rules['email'] = 'required|unique:users';
        }

        $validator = validator($input, $rules);

        if($validator->fails()) {
            abort(500, $validator->messages()->first());
        }

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

            $updatePass = false;
            if (!empty($input['password']) && !Hash::check($input['password'], $data->password)) {
                $updatePass = true;
            }
            User::where([
                ['id', $uuid]
            ])->update([
                'email'         => $input['email'],
                'password'      => $updatePass ? $password_hash : $data->password,
                'updated_at'    => now()->timestamp,
            ]);

            DB::commit();

            $user = User::find($uuid);
            // $user->tokens()->delete();

            // $user_type = $user->karyawan->user_type;
            // $user_type = Str::snake($user_type->type);

            // $token = $user->createToken('PresensiToken', $this->enumType($user_type))->plainTextToken;

            $responseOutput['success'] = true;
            $responseOutput['message'] = trans('response.success.update_karyawan');
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

            DB::commit();

            $responseOutput['success'] = true;
            $responseOutput['message'] = trans('response.success.delete_karyawan');

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function getListClients(Request $request) {
        $responseOutput = $this->responseOutput;

        try {
            $klien = Karyawan::where('type_id', 2)->get();

            $responseOutput['success'] = true;
            $responseOutput['message'] = trans('response.success.get_karyawan');
            $responseOutput['data'] = $klien;

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    public function search(Request $request) {
        $responseOutput = $this->responseOutput;
        $input = $request->all();

        $karyawans = User::whereHas('karyawan', function($query) use ($input) {
            $query->where('type_id', $input['keyword'] ?? 5);
        })->get();

        $lists = [];
        foreach($karyawans as $karyawan) {
            $lists[] = [
                'id' => $karyawan->karyawan->id,
                'fullname' => $karyawan->karyawan->fullname,
                'position' => $karyawan->karyawan->position,
                'email' => $karyawan->email,
                'phone_number' => $karyawan->karyawan->phone_number,
                'user_type' => $karyawan->karyawan->user_type,
                'perusahaan' => $karyawan->karyawan->perusahaan,
            ];
        }
        $responseOutput['success'] = true;
        $responseOutput['message'] = trans('response.success.get_karyawan');
        $responseOutput['data'] = $lists;

        return response()->json($responseOutput);
    }

    public function report(Request $request) {
        $responseOutput = $this->responseOutput;

        $datas = User::whereHas('karyawan', function ($query) {
            $query->whereIn("type_id", [4, 5]);
        })->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Mapping Data
        $t_head = [
            '#',
            'Nama Karyawan',
            'Perusahaan',
            'Status',
            'Email',
            'Phone Number',
        ];

        $start_row = 1;
        foreach ($t_head as $column => $head) {
            $row_col = CellCoordinate::stringFromColumnIndex($column+1).$start_row;
            $sheet->setCellValue($row_col, $head);
            $spreadsheet
                ->getActiveSheet()
                ->getStyle($row_col)
                ->getBorders()
                ->getOutline()
                ->setBorderStyle(Border::BORDER_THIN)
                ->setColor(new Color('00000000'));
        }

        $no = 1;
        $start_row++;
        foreach($datas as $data) {
            // Mapping
            $sheet->setCellValue("A$start_row", $no);
            $sheet->setCellValue("B$start_row", $data->karyawan->fullname);
            $sheet->setCellValue("C$start_row", $data->karyawan->perusahaan->nama_perusahaan);
            $sheet->setCellValue("D$start_row", $data->karyawan->user_type->type);
            $sheet->setCellValue("E$start_row", $data->email);
            $sheet->setCellValue("F$start_row", $data->karyawan->phone_number);

            for($i = 1; $i <= 5; $i++) {
                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle(CellCoordinate::stringFromColumnIndex($i).$start_row)
                    ->getBorders()
                    ->getOutline()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->setColor(new Color('00000000'));
            }
            $no++;
            $start_row++;
        }

        $filename = "Data Karyawan.xlsx";
        $path = storage_path("/app/public/$filename");
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        $responseOutput['success'] = true;
        $responseOutput['message'] = trans('response.success.get_presensi_list');
        $responseOutput['data'] = [
            'url_download' => asset("/storage/$filename"),
        ];

        return response()->json($responseOutput);
    }
}
