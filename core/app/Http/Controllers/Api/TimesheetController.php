<?php

namespace App\Http\Controllers\Api;

use App\Models\UserType;
use App\Models\Timesheet;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TimesheetController extends Controller
{
    public function store(Request $request) {
        $responseOutput = $this->responseOutput;

        $user = Auth::user();
        $input = $request->all();

        DB::beginTransaction();
        try {
            $input['karyawan_id']   = $user->id;
            $input['created_at']    = now()->timestamp;

            $id = Timesheet::insertGetId($input);
            $timesheet = Timesheet::find($id);

            // Remove ID
            unset($timesheet->id);

            $responseOutput['success'] = true;
            $responseOutput['message'] = 'Success! The timesheet has been created. However, you need to wait for approval from the client';
            $responseOutput['data'] = $timesheet;

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
                $responseOutput['message'] = 'Update Successful! The timesheet has been approved by HR.';
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

            $responseOutput['success']  = true;
            $responseOutput['message']  = 'Great! Your timesheet has been successfully loaded.';
            $responseOutput['data']     = $timesheet;

            return response()->json($responseOutput);
        } catch(\Exception $e) {
            abort(500, $e->getMessage());
        }
    }
}
