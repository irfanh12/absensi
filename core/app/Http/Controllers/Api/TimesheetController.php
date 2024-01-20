<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;

use App\Models\Helper;
use App\Models\Timesheet;
use App\Models\TimesheetRevision;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TimesheetController extends Controller
{

    public function lists(Request $request) {
        $responseOutput = $this->responseOutput;
        $input = $request->all();
        $user = Auth::user();

        $query = Timesheet::whereHas('karyawan', function ($query) use ($user) {
            $clause = [
                ["type_id", 5],
            ];
            if ($user->type_id == 2) {
                $clause[] = ["perusahaan_id", $user->karyawan->perusahaan_id];
            }
            $query->where($clause);
        });

        $lists = $query->paginate($input['per_page'] ?? 10);

        $responseOutput['success'] = true;
        $responseOutput['message'] = trans('response.success.get_timesheet_list');
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

    public function approve(Request $request) {
        $responseOutput = $this->responseOutput;

        $user = Auth::user();
        $input = $request->all();

        DB::beginTransaction();
        try {
            $timesheet = Timesheet::find($input['id']);

            $karyawan = $user->karyawan;
            $user_type = $karyawan->user_type;

            if($timesheet) {
                $status = in_array($user_type->type, ['Administrator', 'Human Resource']) ? 2 : 1;

                $timesheet->status = $status;
                $timesheet->updated_at = now()->timestamp;
                $timesheet->save();

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

    public function reject(Request $request) {
        $responseOutput = $this->responseOutput;

        $user = Auth::user();
        $input = $request->all();

        DB::beginTransaction();
        try {
            $timesheet = Timesheet::find($input['id']);

            $karyawan = $user->karyawan;
            $user_type = $karyawan->user_type;

            if($timesheet) {
                $status = in_array($user_type->type, ['Administrator', 'Human Resource']) ? 4 : 3;

                $timesheet->status = $status;
                $timesheet->updated_at = now()->timestamp;
                $timesheet->save();

                TimesheetRevision::insert([
                    'timesheet_id' => $input['id'],
                    'remark_revision' => $input['remark_revision'],
                    'karyawan_id' => $timesheet->karyawan_id,
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
