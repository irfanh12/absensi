<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\JamKerja;
use App\Models\Presensi;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class JamKerjaController extends Controller
{
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
            $responseOutput['message'] = 'Success';
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

        DB::beginTransaction();
        try {
            $dateDay = $this->dateDay($dateDay);
            $jamkerja = JamKerja::where('hari', 'like', "%$dateDay%")->first();

            $presensi = Presensi::where([
                ['jamkerja_id', $jamkerja->id],
                ['karyawan_id', $user->id],
            ])->exists();
            $start_time = !$presensi ? true : false;

            $presensi = new Presensi();
            $presensi->status           = $start_time ? 'Start Time' : 'End Time';
            $presensi->jamkerja_id      = $jamkerja->id;
            $presensi->karyawan_id      = $user->id;
            $presensi->photo            = $this->storeImage($input['photo']['base64image']);
            $presensi->map_direction    = json_encode($input['map_direction']);
            $presensi->time             = $input['time'];
            $presensi->created_at       = now()->timestamp;
            $presensi->save();

            // Remove ID
            unset($presensi->id);

            $responseOutput['success'] = true;
            $responseOutput['message'] = 'Success';
            $responseOutput['data'] = $presensi;

            DB::commit();

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }
}
