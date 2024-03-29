<?php

use App\Models\Ratio;
use App\Models\Series;
use App\Models\BuPerDiv;
use App\Models\Attendance;
use Illuminate\Support\Str;
use App\Models\BatchTracker;
use App\Models\UploadedFile;
use App\Models\ApprovalPerDiv;
use Illuminate\Support\Facades\DB;

function clearQueueTables($batchType)
{
    if ($batchType == 'attendance') {
        if (Attendance::count() != 0) Attendance::truncate();
    };

    $batchTrack = BatchTracker::where('type', $batchType)->first();
    if ($batchTrack) $batchTrack->delete();
}

function getRatioPerDiv($series, $division, $entity)
{
    return Ratio::where('series', $series)
        ->where('division', $division)
        ->where('entity', $entity)
        ->get();
}

function getRatioAverages($ratioPerDiv, $entity)
{
    return [
        $entity . '_' . 'ratio' => round($ratioPerDiv->avg('attendance_ratio'), 2) ?? 0.0,
        $entity . '_' . 'absent_ratio' => round($ratioPerDiv->avg('absent_ratio'), 2) ?? 0.0,
        $entity . '_' . 'sl_percentage' => round($ratioPerDiv->avg('sl_percentage'), 2) ?? 0.0,
        $entity . '_' . 'vl_percentage' => round($ratioPerDiv->avg('vl_percentage'), 2) ?? 0.0,
        $entity . '_' . 'lwop_percentage' => round($ratioPerDiv->avg('lwop_percentage'), 2) ?? 0.0,
        $entity . '_' . 'late_percentage' => round($ratioPerDiv->avg('late_percentage'), 2) ?? 0.0,
        $entity . '_' . 'early_exit_percentage' => round($ratioPerDiv->avg('early_exit_percentage'), 2) ?? 0.0
    ];
}

function notifyInitial($division, $seriesId, $notifMsg, $subject)
{
    try {
        $buPerDivs = BuPerDiv::select('div_head', 'division')
            ->orderBy('div_head', 'asc')
            ->get();
        $seriesDetails = Series::select('series')
            ->where('id', $seriesId)
            ->first();

        foreach ($buPerDivs as $buPerDiv) {
            $ncflRatioPerDiv = getRatioPerDiv($seriesDetails->series, $buPerDiv->division, 'NCFL');

            $npflRatioPerDiv = getRatioPerDiv($seriesDetails->series, $buPerDiv->division, 'NPFL');

            $ncflRatioAverages = getRatioAverages($ncflRatioPerDiv, 'ncfl');
            $npflRatioAverages = getRatioAverages($npflRatioPerDiv, 'npfl');

            $to = $buPerDiv->div_head;
            $division = $buPerDiv->division;
            $notifMsg = $notifMsg;
            $subject = $subject;
            $address = "http://10.216.2.202/hrar_notifier_dev/notify_initial.php";
            $series = $seriesDetails->series;
            $approvalSeriesId = $series . str_replace(" ", "_", $buPerDiv->division);

            ApprovalPerDiv::upsert([
                'series_id' => $approvalSeriesId,
                'division' => $buPerDiv->division,
                'status' => 'pending',
                'series' => $series,
                'is_expired' => 'no'
            ], ['series_id'], ['series', 'status', 'reason', 'is_expired']);

            $ncflJsonQueryString = array();
            $npflJsonQueryString = array();

            foreach ($ncflRatioAverages as $key => $val) {
                $ncflJsonQueryString[] = $key . '=' . $val;
            }

            foreach ($npflRatioAverages as $key => $val) {
                $npflJsonQueryString[] = $key . '=' . $val;
            }

            file_get_contents(
                $address
                    . "?to=" . $to
                    . "&notifMsg=" . str_replace(" ", "%20", $notifMsg)
                    . "&subject=" . str_replace(" ", "%20", $subject)
                    . "&division=" . str_replace(" ", "%20", $division)
                    . "&series_id=" . $approvalSeriesId . "&"
                    . "&series=" . $seriesId . "&"
                    .  implode("&", $ncflJsonQueryString)
                    . "&" .  implode("&", $npflJsonQueryString)
            );
        }

        return 'Successfully sent initial notification to BU Heads';
    } catch (Throwable $th) {
        throw $th;
        return 'Failed to send initial notification';
    }
}

function notifyHr($division, $status, $series, $reason)
{
    $address = "http://10.216.2.202/hrar_notifier_dev/notify_hr.php";

    file_get_contents(
        $address
            . "?division=" . str_replace(" ", "%20", $division)
            . "&status=" . $status
            . "&series=" . $series
            . "&reason=" . str_replace(" ", "%20", $reason) ?? ''
    );
}

function getOverallPerDiv($series, $entity)
{
    return DB::select(
        "SELECT 
                division,
                entity,
                series,
                avg(attendance_ratio) as ratio,
                avg(absent_ratio) as absent_ratio,
                avg(sl_percentage) as sl_percentage,
                avg(vl_percentage) as vl_percentage,
                avg(lwop_percentage) as lwop_percentage,
                avg(late_percentage) as late_percentage,
                avg(early_exit_percentage) as early_exit_percentage
            FROM hrardb.ratios
            WHERE series = ? AND entity = ?
            GROUP BY division, entity, series",
        [$series, $entity]
    );
}

function getGrandTotalPerDiv($series)
{
    return DB::select(
        "SELECT 
                division,
                avg(attendance_ratio) as ratio,
                avg(absent_ratio) as absent_ratio,
                avg(sl_percentage) as sl_percentage,
                avg(vl_percentage) as vl_percentage,
                avg(lwop_percentage) as lwop_percentage,
                avg(late_percentage) as late_percentage,
                avg(early_exit_percentage) as early_exit_percentage
            FROM hrardb.ratios
            WHERE series = ?
            GROUP BY division",
        [$series]
    );
}

function getFileDetails($input, $type)
{
    return [
        'fileLabel' => $input->getClientOriginalName(),
        'fileSize' => $input->getSize(),
        'filePath' => $input->getPath(),
        'fileType' => $type
    ];
}

function saveUploadedFile($fileLabel, $fileSize, $filePath, $fileType)
{
    UploadedFile::updateOrCreate([
        'file' => $fileLabel
    ], [
        // 'file' => $fileLabel,
        'size' => $fileSize,
        'path' => $filePath,
        'type' => $fileType,
    ]);
}

function getSuperConfidentials()
{
    $superConfidentials = DB::connection('hris')->select("SELECT FCEMPNO FROM MASTER.MASTERDATA WHERE FNEMPTYPEID = 3 AND FCREASON_FOR_LEAVING = '';");

    $ids = array();

    foreach ($superConfidentials as $superConfidential) {
        $ids[] = $superConfidential->FCEMPNO;
    }

    return $ids;
}

function cell($row)
{
    return Str::title($row);
}
