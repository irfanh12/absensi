<?php

namespace App\Http\Controllers\Api;

use Exception;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Perusahaan;
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

class KlienController extends Controller
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

        $validator = validator($input['klien'], [
            'identify_id' => 'required|unique:karyawan',
            'email' => 'required|email|unique:users',
        ]);
        if($validator->fails()) {
            abort(500, $validator->messages()->first());
        }

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

        $data = User::find($id);
        $input = $request->all();

        $klien = $input['klien'];
        $perusahaan = $input['perusahaan'];

        $rules = [
            'identify_id' => [
                'required',
                Rule::unique('karyawan')->ignore($id)
            ],
            'email' => [
                'required',
                Rule::unique('users')->ignore($id)
            ],
        ];
        if (isset($klien['identify_id']) && $klien['identify_id'] !== $data->karyawan->identify_id) {
            $rules['identify_id'] = 'required|unique:karyawan';
        }

        if (isset($input['email']) && $input['email'] !== $data->email) {
            $rules['email'] = 'required|unique:users';
        }

        $validator = validator($klien, $rules);
        if($validator->fails()) {
            abort(500, $validator->messages()->first());
        }

        $password = $klien['password'] == 'generate' ? Str::random(8) : $klien['password'];
        $password_hash = Hash::make($password);

        DB::beginTransaction();
        try {
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

            $updatePass = false;
            if (!empty($klien['password']) && !Hash::check($klien['password'], $data->password)) {
                $updatePass = true;
            }

            User::where('id', $uuid)->update([
                'email'         => $klien['email'],
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

    public function report(Request $request) {
        $responseOutput = $this->responseOutput;

        $datas = User::whereHas('karyawan', function ($query) {
            $query->where("type_id", 2);
        })->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Mapping Data
        $t_head = [
            '#',
            'Nama Klien',
            'Perusahaan',
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
            $sheet->setCellValue("D$start_row", $data->email);
            $sheet->setCellValue("E$start_row", $data->karyawan->phone_number);

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

        $filename = "Data Klien.xlsx";
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
