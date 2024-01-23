<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\BuPerDiv;
use Illuminate\Http\Request;
use App\Models\ApprovalPerDiv;
use App\Models\Series;

class MailerController extends Controller
{

    public function sendInitialMail(Request $request)
    {
        try {
            $buPerDivs = BuPerDiv::select('div_head', 'division')->get();
            $seriesDetails = Series::select('series')
                ->where('id', $request->query('seriesid'))
                ->first();


            foreach ($buPerDivs as $buPerDiv) {
                $ncflRatioPerDiv = getRatioPerDiv($seriesDetails->series, $buPerDiv->division, 'NCFL');

                $npflRatioPerDiv = getRatioPerDiv($seriesDetails->series, $buPerDiv->division, 'NPFL');

                $ncflRatioAverages = json_encode(getRatioAverages($ncflRatioPerDiv));
                $npflRatioAverages = json_encode(getRatioAverages($npflRatioPerDiv));

                $to = $buPerDiv->div_head;
                $division = $buPerDiv->division;
                $notifMsg = $request->notifMsg;
                $subject = $request->subject;
                $address = "http://10.216.2.202/hrar_notifier/notify_initial.php";
                $series = $seriesDetails->series;

                ApprovalPerDiv::upsert([
                    'series_id' => '2024_1_' . str_replace(" ", "_", $buPerDiv->division),
                    'division' => $buPerDiv->division,
                    'status' => 'pending',
                    'series' => $series,
                    'is_expired' => 'no'
                ], ['series_id'], ['series', 'status', 'reason', 'is_expired']);

                file_get_contents(
                    $address
                        . "?to=" . $to
                        . "&notifMsg=" . str_replace(" ", "%20", $notifMsg)
                        . "&subject=" . str_replace(" ", "%20", $subject)
                        . "&division=" . str_replace(" ", "%20", $division)
                        . "&series=" . $series
                        . "&ncflratio=" . urldecode($ncflRatioAverages)
                        . "&npflratio=" . urldecode($npflRatioAverages)
                );
            }

            return 'Successfully sent initial notification to BU Heads';
        } catch (Throwable $th) {
            throw $th;
            return 'Failed to send initial notification';
        }
    }

    public function getNotifApproval(Request $request)
    {
        $approval = ApprovalPerDiv::where('division', $request->query('division'))
            ->where('series', $request->query('series'))
            // ->where('is_expired', 'no')
            ->first();

        if ($approval->is_expired == 'no') $approval->update(['status' => $request->query('status')]);

        return view('attendance.approval', ['approval' => $approval]);
    }

    public function postRejectionReason(Request $request)
    {

        return redirect('/');
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
