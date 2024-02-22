<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\JamKerja;
use App\Models\Presensi;
use App\Http\Controllers\Controller;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate as CellCoordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;

class JamKerjaController extends Controller
{
    public function lists(Request $request, $dateDay) {
        $responseOutput = $this->responseOutput;
        $input = $request->all();

        $created_from = strtotime($dateDay);
        $created_to = strtotime(date('Y-m-t', strtotime($dateDay)));

        $queries = Presensi::where(fn($query) => $query->where([
            ['karyawan_id', $input['id']],
            ['created_at', '>=', $created_from],
            ['created_at', '<=', $created_to],
        ]))
            ->orderBy('created_at', 'asc')
            ->get();

        $lists = [];
        foreach($queries as $query) {
            $date = date('Y-m-d', $query->created_at);
            $lists[$date][] = [
                'jamkerja' => $query->jamkerja,
                'karyawan' => $query->karyawan,
                'time' => $query->time,
                'status' => $query->status,
                'photo' => $query->photo,
                'map_direction' => $query->map_direction,
                'created_at' => $query->created_at,
            ];
        }

        // dd($lists);
        foreach($lists as $date => &$list) {
            $field['date'] = date('d M Y', strtotime($date));
            $field['karyawan'] = $list[0]['karyawan'];
            $field['time'] = [ $list[0]['time'], $list[1]['time'] ?? null ];
            $field['directions'] = [ $list[0]['map_direction'], $list[1]['map_direction'] ?? null ];
            $field['photos'] = [ $list[0]['photo'], $list[1]['photo'] ?? null ];
            $field['status'] = [ $list[0]['status'], $list[1]['status'] ?? null ];
            $field['status_label'] = Presensi::getStatusTime([
                $list[0]['time'], $list[1]['time'] ?? null
            ], $list[0]['jamkerja']);

            $list = $field;
        }

        // Paginate the collection
        $perPage = $input['per_page']; // Number of items per page
        $page = Paginator::resolveCurrentPage() ?: 1; // Get the current page or default to 1
        $collection = collect($lists)
            ->values();
        $paginatedData = $collection->forPage($page ?? 1, $perPage ?? 10)->values();

        // Create paginator instance
        $paginator = new Paginator($paginatedData, $perPage, $page, [ 'path' => Paginator::resolveCurrentPath() ]);
        $paginator = $paginator->toArray();
        $paginator['last_page'] = max((int) ceil($collection->count() / $perPage), 1);

        $responseOutput['success'] = true;
        $responseOutput['message'] = trans('response.success.get_presensi_list');
        $responseOutput['data'] = $paginator;

        return response()->json($responseOutput);
    }

    /**
     * Generates a JSON response containing the working hours for a given date.
     *
     * @param Request $request The HTTP request object.
     * @param string $jamkerja The date for which to retrieve the working hours.
     * @throws Exception If an error occurs while retrieving the working hours.
     * @return Illuminate\Http\JsonResponse The JSON response containing the working hours.
     */

    public function getWorkHour(Request $request, $dateDay) {
        $responseOutput = $this->responseOutput;

        try {
            $dateDay = $this->dateDay($dateDay);
            $jamkerja = JamKerja::where('hari', 'like', "%$dateDay%")->first();

            $responseOutput['success'] = true;
            $responseOutput['message'] = trans('response.success.post_presensi'); 'Success';
            $responseOutput['data'] = $jamkerja;

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    public function getPresensiEmployee(Request $request, $dateDay) {
        $responseOutput = $this->responseOutput;

        $user = Auth::user();
        $input = $request->all();
        $nowTimestamp = now()->timestamp;

        try {
            $dateDay = $this->dateDay($dateDay);
            $jamkerja = JamKerja::where('hari', 'like', "%$dateDay%")->first();

            $startTime = Carbon::createFromTimestamp($nowTimestamp)->startOfDay()->timestamp;
            $endTime = Carbon::createFromTimestamp($nowTimestamp)->endOfDay()->timestamp;

            $presensi = Presensi::where([
                ['karyawan_id', $user->id],
                ['jamkerja_id', $jamkerja->id],
                ['created_at', '>=', $startTime],
                ['created_at', '<', $endTime],
            ])->get();

            $data = [
                'start_time' => "--:--",
                'end_time' => "--:--",
            ];

            if($presensi->count()) {
                $presensi_start = $presensi->where('status', 'Start Time')->first();
                $presensi_end = $presensi->where('status', 'End Time')->last();
                $data['start_time'] = $presensi_start->time;
                $data['end_time'] = $presensi_end ? $presensi_end->time : '--:--';
            }

            $responseOutput['success']  = true;
            $responseOutput['message']  = 'Success! Your attendance has been successfully loaded.';
            $responseOutput['data']     = $data;

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    public function postPresensiEmployee(Request $request, $dateDay) {
        $responseOutput = $this->responseOutput;

        $user = Auth::user();
        $input = $request->all();
        $dateDateTimestamp = strtotime($dateDay);

        DB::beginTransaction();
        try {
            $dateDay = $this->dateDay($dateDay);
            $jamkerja = JamKerja::where('hari', 'like', "%$dateDay%")->first();

            $presensi = Presensi::where([
                ['jamkerja_id', $jamkerja->id],
                ['created_at', '>=', $dateDateTimestamp],
                ['karyawan_id', $user->id],
            ])->exists();
            $start_time = !$presensi ? true : false;

            $presensi = new Presensi();
            $presensi->status           = $start_time ? 'Start Time' : 'End Time';
            $presensi->jamkerja_id      = $jamkerja->id;
            $presensi->karyawan_id      = $user->id;
            $presensi->photo            = $this->storeImage($input['photo']['base64image']);
            $presensi->map_direction    = $input['map_direction'];
            $presensi->time             = $input['time'];
            $presensi->created_at       = now()->timestamp;
            $presensi->save();

            // Remove ID
            unset($presensi->id);

            $responseOutput['success'] = true;
            $responseOutput['message'] = trans('response.success.post_presensi');
            $responseOutput['data'] = $presensi;

            DB::commit();

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function reportList(Request $request) {
        $responseOutput = $this->responseOutput;
        try {


            $responseOutput['success'] = true;
            $responseOutput['message'] = 'Success!';
            $responseOutput['data'] = [];

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    public function report(Request $request, $dateDay) {
        $responseOutput = $this->responseOutput;
        $input = $request->all();

        $created_from = strtotime($dateDay);
        $created_to = strtotime(date('Y-m-t', strtotime($dateDay)));

        $query = Presensi::select([
            'jamkerja_id',
            'karyawan_id',
            DB::raw('GROUP_CONCAT( status ) as status'),
            DB::raw('GROUP_CONCAT( TIME ) as time'),
            DB::raw('GROUP_CONCAT( map_direction ) as directions'),
            DB::raw('GROUP_CONCAT( photo ) as photos'),
            DB::raw('min( created_at ) as created_at'),
        ])->where(fn($query) => $query->where([
            ['karyawan_id', $input['id']],
            ['created_at', '>=', $created_from],
            ['created_at', '<=', $created_to],
        ]))
        ->groupBy('jamkerja_id', 'karyawan_id');

        $lists = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $t_head = [
            'Nama Karyawan',
            'Status',
            'Posisi',
            'Alamat Email',
            'No Telepon',
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


        // Mapping Data
        $t_head = [
            '#',
            'Tanggal',
            'Status',
            'Masuk',
            'Pulang',
            'Koordinat',
        ];
        $start_row = 8;
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
            $list->status = explode(',', $list->status);
            $list->photos = explode(',', $list->photos);
            $list->directions = json_decode('[' . $list->directions . ']', true);
            $list->time = explode(',', $list->time);

            $jamkerja = JamKerja::find($list->jamkerja_id);
            $list->status_label = Presensi::getStatusTime($list->time ?? null, $jamkerja, true);

            // Mapping
            $sheet->setCellValue("A$start_row", $no);
            $sheet->setCellValue("B$start_row", Carbon::createFromTimestamp($list->created_at)->format('Y-m-d'));
            $sheet->setCellValue("C$start_row", implode(', ', $list->status_label));
            $sheet->setCellValue("D$start_row", $list->time[0]);
            $sheet->setCellValue("E$start_row", $list->time[1] ?? '-');
            $sheet->setCellValue("F$start_row", json_encode($list->directions));

            for($i = 1; $i <= 6; $i++) {
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

        $filename = "Presensi $nama_karyawan Bulan " . date('F Y', strtotime($dateDay)) . ".xlsx";
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
