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
use Illuminate\Support\Facades\Session;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getProgress(Request $request)
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
        $ratioBySeries['data'] =
            Ratio::select(
                'staff_code',
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
            ->where('series', $series->series)
            ->get();

        return $ratioBySeries;
    }

    public function clearBatchingTables(Request $request)
    {
        clearQueueTables($request->query('type'));
        return 'success';
    }

    public function downloadFile(Request $request)
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
            ->whereDate('updated_at', Carbon::today())
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

    public function export(Request $request)
    {
        $seriesDetails = Series::where('id', $request->query('series_id'))->first();
        $ratioBySeries = $this->getRatioBySeries($request);
        return Excel::download(new AttendanceDownload($ratioBySeries), $seriesDetails->series . '_ratio.xlsx');
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

        DB::beginTransaction();
        try {
            (new StaffBaseDetailsUpload($fileDetails))
                ->queue($request->file('upload_base')->storeAs('files/base', $fileLabel));

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return redirect('/');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'upload' => ['required', new FileNotMatch, new NoStaffBaseDataFound],
        ]);

        $requestedFile = $request->file('upload');
        $fileLabel = $request->file('upload')->getClientOriginalName();
        $fileType = Str::contains($fileLabel, 'PR') ? 'agency_attendance' : 'allsec_attendance';
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
