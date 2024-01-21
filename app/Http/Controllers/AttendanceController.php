<?php

namespace App\Http\Controllers;

use Throwable;
use App\Rules\FileNotMatch;
use Illuminate\Http\Request;
use App\Imports\AttendanceUpload;
use Illuminate\Support\Facades\DB;
use App\Rules\NoStaffBaseDataFound;
use App\Imports\StaffBaseDetailsUpload;
use App\Models\BatchTracker;
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('attendance.index');
    }

    public function uploadBase(Request $request)
    {
        $request->validate([
            'upload_base' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // Excel::import(new AttendanceUpload, $request->file('upload')->store('files'));
            (new StaffBaseDetailsUpload)->queue($request->file('upload_base')->store('files/base'));

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

        $fileLabel = $request->file('upload')->getClientOriginalName();
        $notificationMsg = null;

        DB::beginTransaction();
        try {
            (new AttendanceUpload($fileLabel, $notificationMsg))->queue($request->file('upload')->store('files/raw'));
            Session::put('success', $notificationMsg);
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
