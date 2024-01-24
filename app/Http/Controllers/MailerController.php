<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessFollowup;
use Throwable;
use App\Models\BuPerDiv;
use Illuminate\Http\Request;
use App\Models\ApprovalPerDiv;
use App\Models\Series;

class MailerController extends Controller
{

    public function sendInitialMail(Request $request)
    {
        $divsNeedsApprovals = ApprovalPerDiv::where('status', 'pending')
            ->orWhere(function ($query) {
                $query->where('status', 'rejected')
                    ->where('reason', '');
            })
            ->where('is_expired', 'no')
            ->get();

        ProcessFollowup::dispatch($divsNeedsApprovals)
            ->delay(now()->addMinutes(1));

        return notifyInitial($request->query('division'), $request->query('seriesid'), $request->notifMsg, $request->subject);
    }

    public function getNotifApproval(Request $request)
    {
        $approval = ApprovalPerDiv::where('division', $request->query('division'))
            ->where('series_id', $request->query('series'))
            // ->where('is_expired', 'no')
            ->first();


        if ($approval->is_expired == 'no') $approval->update(['status' => $request->query('status')]);

        notifyHr($request->query('division'), $request->query('status'), $approval->series, '');

        return view('attendance.approval', ['approval' => $approval]);
    }

    public function postRejectionReason(Request $request, $id)
    {
        $approvalDetails = ApprovalPerDiv::where('id', $id)->first();

        $approvalDetails->update(['reason' => $request->reason]);

        notifyHr($approvalDetails->division, $approvalDetails->status, $approvalDetails->series, $request->reason);

        return redirect()->back()->with('status', 'Successfuly posted your rejection reason');;
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
