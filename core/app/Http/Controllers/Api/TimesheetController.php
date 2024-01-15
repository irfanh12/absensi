<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;

use App\Models\Helper;
use App\Models\Timesheet;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TimesheetController extends Controller
{
    /**
     * Stores the timesheet data for a given user.
     *
     * @param Request $request The request object containing the timesheet data.
     * @param mixed $dateDay The date or day for which the timesheet is being stored.
     * @throws \Exception If an error occurs while storing the timesheet.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the success status, message, and time information.
     */
    public function store(Request $request) {
        $responseOutput = $this->responseOutput;

        $user = Auth::user();
        $input = $request->all();

        DB::beginTransaction();
        try {
            $input['karyawan_id'] = $user->id;
            $input['created_at'] = now()->timestamp;

            Timesheet::insert($input);

            $responseOutput['success'] = true;
            $responseOutput['message'] = trans('response.success.post_timesheet');
            $responseOutput['time_at'] = now()->diffForHumans();

            DB::commit();

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function approve(Request $request, $uuid) {
        $responseOutput = $this->responseOutput;

        $user = Auth::user();
        $input = $request->all();

        DB::beginTransaction();
        try {
            $timesheet = Timesheet::where([
                ['karyawan_id', $uuid],
                ['status', 0],
            ])->first();

            $karyawan = $user->karyawan;
            $user_type = $karyawan->user_type;

            if($timesheet) {
                $status = in_array($user_type->type, ['Administrator', 'Human Resource']) ? 2 : 1;

                $timesheet->status = $status;
                $timesheet->updated_at = now()->timestamp;
                $timesheet->save();

                // Remove ID
                unset($timesheet->id);

                $responseOutput['success'] = true;
                $responseOutput['message'] = trans('response.success.post_approve', ['name' => $user_type->type]);
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
        $nowTimestamp = now()->timestamp;

        try {
            $timesheet = Timesheet::where([
                ['karyawan_id', $user->id],
                ['created_at', '<=', $nowTimestamp],
            ])->limit(4)
                ->orderBy('created_at', 'desc')
                ->get();

            $store_timesheet = false;
            if($timesheet->count() > 0) {
                $firstTimestamp = $timesheet->first()->created_at;
                $store_timesheet = Helper::unixTimeToDate($firstTimestamp) == Helper::unixTimeToDate($nowTimestamp);
            }

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
}
