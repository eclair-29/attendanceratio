<?php

namespace App\Http\Controllers;

use Throwable;
use Carbon\Carbon;
use App\Models\Ratio;
use App\Models\Series;
use App\Rules\FileNotMatch;
use Illuminate\Support\Str;
use App\Models\BatchTracker;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Models\ApprovalPerDiv;
use App\Imports\AttendanceUpload;
use Illuminate\Support\Facades\DB;
use App\Exports\AttendanceDownload;
use App\Rules\NoStaffBaseDataFound;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StaffBaseDetailsUpload;
use App\Models\BuPerDiv;
use App\Models\StaffBaseDetail;
use Illuminate\Support\Facades\Session;

class AttendanceController extends Controller
{
    public function getFileUploadProgress(Request $request)
    {
        $currentBatch = BatchTracker::where('type', $request->type)->first();
        return $currentBatch;
    }

    public function getSeries()
    {
        $series = Series::all()->orderBy('id', 'desc');
        return $series;
    }

    public function getRatioBySeries(Request $request)
    {
        $series = Series::where('id', $request->query('series_id'))->first();
        $superConfidentials = getSuperConfidentials();
        $staffcodes = StaffBaseDetail::pluck('staff_code')->all();
        $ratioBySeries['data'] =
            Ratio::select(
                'staff_code',
                'staff',
                'division',
                'dept',
                'section',
                'entity',
                'attendance_ratio',
                'absent_ratio',
                'total_sl',
                'total_vl',
                'total_lwop',
                'total_late',
                'total_early_exit',
                'sl_percentage',
                'vl_percentage',
                'lwop_percentage',
                'late_percentage',
                'early_exit_percentage'
            )
            // ->whereIn('staff_code', $staffcodes)
            ->whereNotIn('staff_code', $superConfidentials)
            ->where('series', $series->series)
            ->get();

        return $ratioBySeries;
    }

    public function getRatioBySeriesAndDiv(Request $request)
    {
        $series = Series::where('id', $request->query('series'))->first();
        $superConfidentials = getSuperConfidentials();

        $ratioBySeriesAndDiv['data'] =
            Ratio::select(
                'staff_code',
                'staff',
                'division',
                'dept',
                'section',
                'entity',
                'attendance_ratio',
                'absent_ratio',
                'total_sl',
                'total_vl',
                'total_lwop',
                'total_late',
                'total_early_exit',
                'sl_percentage',
                'vl_percentage',
                'lwop_percentage',
                'late_percentage',
                'early_exit_percentage'
            )
            ->whereNotIn('staff_code', $superConfidentials)
            ->where('series', $series->series)
            ->where('division', $request->query('division'))
            ->get();

        return $ratioBySeriesAndDiv;
    }

    public function clearBatchingTables(Request $request)
    {
        clearQueueTables($request->query('type'));
        return 'success';
    }

    public function downloadUploadedFile(Request $request)
    {
        $file = $request->query('file');
        $type = $request->query('type') == 'attendance' ? 'raw' : 'base';
        $path = storage_path('app/files/' . $type . '/' . $file);
        return response()->download($path);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seriesList = Series::all();
        $currentUploadedBaseFile = UploadedFile::where('type', 'base_data')
            ->orderBy('created_at', 'desc')
            ->first();

        $recentUploadedAttendanceFile = UploadedFile::where('type', 'like', '%attendance%')
            // ->whereDate('updated_at', Carbon::today())
            ->orderBy('updated_at', 'desc')
            ->get();

        $uploadedFileHistory = UploadedFile::where('type', 'like', '%attendance%')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('attendance.index', [
            'seriesList' => $seriesList,
            'recentUploadedAttendanceFile' => $recentUploadedAttendanceFile,
            'uploadedFileHistory' => $uploadedFileHistory,
            'currentUploadedBaseFile' => $currentUploadedBaseFile
        ]);
    }

    public function exportBySeries(Request $request)
    {
        $seriesDetails = Series::where('id', $request->query('series_id'))->first();
        $ratioBySeries = $this->getRatioBySeries($request);

        foreach ($ratioBySeries['data'] as $value) {
            $value->attendance_ratio /= 100;
            $value->absent_ratio /= 100;
            $value->sl_percentage /=  100;
            $value->vl_percentage /= 100;
            $value->lwop_percentage /= 100;
            $value->late_percentage /= 100;
            $value->early_exit_percentage /= 100;
        }

        return Excel::download(new AttendanceDownload($ratioBySeries), $seriesDetails->series . '_ratio.xlsx');
    }

    public function exportByDivision(Request $request)
    {
        $seriesDetails = Series::where('id', $request->query('series'))->first();
        $ratioBySeriesAndDiv = $this->getRatioBySeriesAndDiv($request);

        foreach ($ratioBySeriesAndDiv['data'] as $value) {
            $value->attendance_ratio /= 100;
            $value->absent_ratio /= 100;
            $value->sl_percentage /=  100;
            $value->vl_percentage /= 100;
            $value->lwop_percentage /= 100;
            $value->late_percentage /= 100;
            $value->early_exit_percentage /= 100;
        }

        return Excel::download(new AttendanceDownload($ratioBySeriesAndDiv), $request->query('division') . ' ' . date('F Y', strtotime(str_replace('_', '-', $seriesDetails->series))) . ' Attendance Ratio.xlsx');
    }

    public function uploadBase(Request $request)
    {
        $request->validate([
            'upload_base' => 'required',
        ]);

        $fileLabel = $request->file('upload_base')->getClientOriginalName();
        $requestedFile = $request->file('upload_base');
        $fileType = 'base_data';
        $fileDetails = getFileDetails($requestedFile, $fileType);

        // DB::beginTransaction();
        try {
            (new StaffBaseDetailsUpload($fileDetails))
                ->queue($request->file('upload_base')->storeAs('files/base', $fileLabel));

            // DB::commit();
        } catch (Throwable $th) {
            // DB::rollBack();
            // throw $th;
            dd($th->getMessage());
            return 'Failed to upload base data due to: ' . $th->getMessage();
        }
        return redirect('/');
    }

    public function uploadAttendance(Request $request)
    {
        $request->validate([
            'upload' => ['required', new FileNotMatch, new NoStaffBaseDataFound],
        ]);

        $requestedFile = $request->file('upload');
        $fileLabel = $request->file('upload')->getClientOriginalName();
        $fileType = Str::contains($fileLabel, config('constants.agency_prefix'))
            ? 'agency_attendance'
            : 'allsec_attendance';
        $fileDetails = getFileDetails($requestedFile, $fileType);

        DB::beginTransaction();
        try {
            (new AttendanceUpload($fileLabel, $fileDetails))
                ->queue($request->file('upload')->storeAs('files/raw', $fileLabel));

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return redirect('/');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
