<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;

use App\Models\Helper;
use App\Models\User;
use App\Models\Presensi;
use App\Models\Timesheet;
use App\Models\JamKerja;
use App\Models\TimesheetRevision;
use App\Http\Controllers\Controller;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate as CellCoordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TimesheetController extends Controller
{

    public function lists(Request $request, $dateDay) {
        $responseOutput = $this->responseOutput;
        $input = $request->all();

        $user = Auth::user();

        $created_from = strtotime($dateDay);
        $created_to = strtotime(date('Y-m-t', strtotime($dateDay)));

        $query = Timesheet::where(fn($query) => $query->where([
            ['karyawan_id', $input['id']],
            ['created_at', '>=', $created_from],
            ['created_at', '<=', $created_to],
        ]));

        $lists = $query->paginate($input['per_page'] ?? 10);

        $karyawan = User::whereHas('karyawan', function($query) use ($input) {
            $query->where('id', $input['id']);
        })->first();
        $klien = User::whereHas('karyawan', function($query) use ($karyawan) {
            $query->where([
                ['perusahaan_id', $karyawan->karyawan->perusahaan->id],
                ['type_id', 2],
            ]);
        })->first();

        $karyawan = $user->karyawan;
        $user_type = $karyawan->user_type;

        $status = in_array($user_type->type, ['Administrator', 'Human Resource']) ? 1 : 0;
        $canApproveReject = Timesheet::where([
            ['created_at', '>=', $created_from],
            ['status', $status]
        ])->count();

        $lists = $lists->toArray();

        $lists['canApproveReject'] = $canApproveReject > 0;
        $lists['klien'] = [
            'fullname' => $klien->karyawan->fullname,
            'email' => $klien->email,
        ];
        $lists['revision'] = TimesheetRevision::where([
            ['karyawan_id', $input['id']],
            ['date_month', '=', $dateDay],
        ])->get();

        $responseOutput['success'] = true;
        $responseOutput['message'] = trans('response.success.get_presensi_list');
        $responseOutput['data'] = $lists;

        return response()->json($responseOutput);
    }

    /**
     * Stores the timesheet data for a given user.
     *
     * @param Request $request The request object containing the timesheet data.
     * @param mixed $dateDay The date or day for which the timesheet is being stored.
     * @throws \Exception If an error occurs while storing the timesheet.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the success status, message, and time information.
     */
    public function store(Request $request) {
        $response = $this->responseOutput;
        $user = Auth::user();
        $input = $request->all();

        DB::beginTransaction();
        try {
            $input['karyawan_id'] = $user->id;

            $timesheet = Timesheet::find($input['id']);
            if ($timesheet) {
                $input['status'] = 0;
                $input['updated_at'] = now()->timestamp;
                $timesheet->update($input);
            } else {
                unset($input['id']);
                $input['created_at'] = now()->timestamp;
                Timesheet::insert($input);
            }

            $response['success'] = true;
            $response['message'] = trans('response.success.post_timesheet');
            $response['time_at'] = now()->diffForHumans();

            DB::commit();

            return response()->json($response);
        } catch(\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function approve(Request $request, $dateDay) {
        $responseOutput = $this->responseOutput;

        $user = Auth::user();
        $input = $request->all();

        $created_from = strtotime($dateDay);
        $created_to = strtotime(date('Y-m-t', strtotime($dateDay)));

        DB::beginTransaction();
        try {
            $karyawan = $user->karyawan;
            $user_type = $karyawan->user_type;

            $status = in_array($user_type->type, ['Administrator', 'Human Resource']) ? 2 : 1;
            $status_in = in_array($user_type->type, ['Administrator', 'Human Resource']) ? 1 : 0;

            $timesheet = Timesheet::where([
                ['karyawan_id', $input['id']],
                ['status', $status_in],
                ['created_at', '>=', $created_from],
                ['created_at', '<=', $created_to],
            ])->update([
                'status' => $status,
                'updated_at' => now()->timestamp
            ]);

            $responseOutput['success'] = true;
            $responseOutput['message'] = trans('response.success.post_approve', ['name' => $user_type->type]);
            $responseOutput['data'] = $timesheet;

            DB::commit();

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function reject(Request $request, $dateDay) {
        $responseOutput = $this->responseOutput;

        $user = Auth::user();
        $input = $request->all();

        $created_from = strtotime($dateDay);
        $created_to = strtotime(date('Y-m-t', strtotime($dateDay)));

        DB::beginTransaction();
        try {
            $timesheet = Timesheet::where([
                ['karyawan_id', $input['id']],
                ['created_at', '>=', $created_from],
            ])->get();

            $karyawan = $user->karyawan;
            $user_type = $karyawan->user_type;

            if($timesheet) {
                $status = in_array($user_type->type, ['Administrator', 'Human Resource']) ? 4 : 3;

                Timesheet::where([
                    ['karyawan_id', $input['id']],
                    ['status', 0],
                    ['created_at', '>=', $created_from],
                    ['created_at', '<=', $created_to],
                ])->update([
                    'status' => $status,
                    'updated_at' => now()->timestamp,
                ]);

                TimesheetRevision::insert([
                    'date_month' => $dateDay,
                    'remark_revision' => $input['remark_revision'],
                    'karyawan_id' => $input['id'],
                    'created_at' => now()->timestamp,
                ]);

                $responseOutput['success'] = true;
                $responseOutput['message'] = trans('response.success.post_reject', ['name' => $user_type->type]);
                $responseOutput['data'] = $timesheet;

                DB::commit();

                return response()->json($responseOutput);
            }

            abort(500, $responseOutput['message']);
        } catch(\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function getTimesheetEmployee(Request $request) {
        $responseOutput = $this->responseOutput;

        $user = Auth::user();
        $nowTimestamp = strtotime($request->date);

        try {
            $timesheet = Timesheet::where([
                ['karyawan_id', $user->id],
                ['created_at', '<=', now()->timestamp],
            ])->limit(4)
                ->orderBy('created_at', 'desc')
                ->get();

            // if($timesheet->count() > 0) {
            //     $firstTimestamp = $timesheet->first()->created_at;
            //     $store_timesheet = Helper::unixTimeToDate($firstTimestamp) == Helper::unixTimeToDate($nowTimestamp);
            // }

            $presensi = Presensi::where([
                ['karyawan_id', $user->id],
                ['created_at', '<=', $nowTimestamp],
            ])->limit(4)
                ->orderBy('created_at', 'desc')
                ->get();

            if($presensi->count() > 0) {
                $firstTimestamp = $presensi->first()->created_at;
                // dd(Helper::unixTimeToDate($firstTimestamp), Helper::unixTimeToDate($nowTimestamp));
                $store_timesheet = Helper::unixTimeToDate($firstTimestamp) != Helper::unixTimeToDate($nowTimestamp);
            }

            $store_timesheet = Timesheet::where([
                ['karyawan_id', $user->id],
                ['created_at', '>=', $nowTimestamp],
            ])->exists();

            $responseOutput['success'] = true;
            $responseOutput['message'] = trans('response.success.get_timesheet');
            $responseOutput['data'] = [
                'store_timesheet' => $store_timesheet,
                'items' => $timesheet
            ];

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    public function report(Request $request, $dateDay) {

        $responseOutput = $this->responseOutput;
        $input = $request->all();

        $user = Auth::user();

        $created_from = strtotime($dateDay);
        $created_to = strtotime(date('Y-m-t', strtotime($dateDay)));

        $query = Timesheet::where(fn($query) => $query->where([
            ['karyawan_id', $input['id']],
            ['created_at', '>=', $created_from],
            ['created_at', '<=', $created_to],
        ]));

        $lists = $query->get();

        $klien = User::whereHas('karyawan', function($query) use ($input) {
            $query->where([
                ['perusahaan_id', $input['perusahaan']['id']],
                ['type_id', 2],
            ]);
        })->first();

        $karyawan = $user->karyawan;
        $user_type = $karyawan->user_type;

        $status = in_array($user_type->type, ['Administrator', 'Human Resource']) ? 2 : 1;
        $isApprove = Timesheet::where(fn($query) => $query->where([
            ['status', $status]
        ]))->count();

        $isReject = Timesheet::where(fn($query) => $query->where([
            ['status', 3]
        ]))->count();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $t_head = [
            'Nama Karyawan',
            'Status',
            'Posisi',
            'Alamat Email',
            'No Telepon',
            'Status Timesheet',
        ];

        $start_row = 2;
        foreach ($t_head as $head) {
            $sheet->setCellValue("A$start_row", $head);
            $start_row++;
        }
        $sheet->setCellValue("C2", $input['fullname']);
        $sheet->setCellValue("C3", $input['user_type']['type']);
        $sheet->setCellValue("C4", $input['position']);
        $sheet->setCellValue("C5", $input['email']);
        $sheet->setCellValue("C6", $input['phone_number']);
        $sheet->setCellValue("C7", $isApprove > 0 ? 'Approved' : ($isReject > 0 ? 'Rejected' : 'Pending'));

        // Mapping Data
        $t_head = [
            '#',
            'Tanggal',
            'Klien',
            'Status',
            'Kegiatan',
        ];
        $start_row = 9;
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
        foreach($lists as &$list) {

            // Mapping
            $sheet->setCellValue("A$start_row", $no);
            $sheet->setCellValue("B$start_row", Carbon::createFromTimestamp($list->created_at)->format('Y-m-d'));
            $sheet->setCellValue("C$start_row", $klien->karyawan->fullname);
            $sheet->setCellValue("D$start_row", $list->status['label']);
            $sheet->setCellValue("E$start_row", $list->remarks);

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

        $nama_karyawan = $input['fullname'];

        $filename = "Timesheet $nama_karyawan Bulan " . date('F Y', strtotime($dateDay)) . ".xlsx";
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
