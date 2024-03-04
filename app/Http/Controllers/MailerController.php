<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessFinalRelease;
use App\Jobs\ProcessFollowup;
use Throwable;
use App\Models\BuPerDiv;
use Illuminate\Http\Request;
use App\Models\ApprovalPerDiv;
use App\Models\Series;

class MailerController extends Controller
{
    public function sendMailNotification(Request $request)
    {
        $seriesDetails = Series::where("id", $request->query('seriesid'))
            ->first();

        $overallNcfl = getOverallPerDiv($seriesDetails->series, 'NCFL');
        $overallNpfl = getOverallPerDiv($seriesDetails->series, 'NPFL');
        $overall = getGrandTotalPerDiv($seriesDetails->series);

        $buDetails = BuPerDiv::all();
        $to = array();

        foreach ($buDetails as $bu) {
            $to[] = $bu->div_head;
        }

        ProcessFollowup::dispatch($seriesDetails->series)
            ->delay(now()->addHours(12));

        ProcessFinalRelease::dispatch($overallNcfl, $overall, $to, $seriesDetails->series)
            ->delay(now()->addHours(48));

        return notifyInitial($request->query('division'), $request->query('seriesid'), $request->notifMsg, $request->subject);
    }

    public function downloadRatioByDivision(Request $request)
    {
        return view('attendance.download', [
            'division' => $request->query('division'),
            'series' => $request->query('series'),
        ]);
    }

    public function getApprovalNotification(Request $request)
    {
        $approval = ApprovalPerDiv::where('division', $request->query('division'))
            ->where('series_id', $request->query('series'))
            // ->where('is_expired', 'no')
            ->first();

        if ($approval->is_expired == 'no') $approval->update(['status' => $request->query('status')]);

        notifyHr($request->query('division'), $request->query('status'), $approval->series, '');

        return view('attendance.approval', ['approval' => $approval]);
    }

    public function getRejectionFeedback()
    {
        return view('attendance.feedback');
    }

    public function postRejectionReason(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required',
        ]);

        $approvalDetails = ApprovalPerDiv::where('id', $id)->first();

        // if ($request->file('division_ratio_changes')) {
        $fileLabel = $request->file('division_ratio_changes')->getClientOriginalName();
        $request->file('division_ratio_changes')->storeAs('files/attendance_changes', $fileLabel);
        // }

        $approvalDetails->update([
            'reason' => $request->reason,
            'changes_file_path' => 'files/attendance_changes/' . $fileLabel,
        ]);

        notifyHr($approvalDetails->division, $approvalDetails->status, $approvalDetails->series, $request->reason);

        return redirect('/notifications/feedback');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
