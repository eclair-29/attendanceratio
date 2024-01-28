<?php

namespace App\Jobs;

use App\Models\ApprovalPerDiv;
use App\Models\BuPerDiv;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessFollowup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $divsNeedsApprovals;
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($divsNeedsApprovals)
    {
        $this->divsNeedsApprovals = $divsNeedsApprovals;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->divsNeedsApprovals as $approval) {
            $ncflRatioPerDiv = getRatioPerDiv($approval->series, $approval->division, 'NCFL');

            $npflRatioPerDiv = getRatioPerDiv($approval->series, $approval->division, 'NPFL');

            $ncflRatioAverages = getRatioAverages($ncflRatioPerDiv, 'ncfl');
            $npflRatioAverages = getRatioAverages($npflRatioPerDiv, 'npfl');

            $divisionHeadMail = BuPerDiv::where('division', $approval->division)->first();

            $to = $divisionHeadMail->div_head;
            $division = $approval->division;
            $notifMsg = 'A gentle followup on the approval of your attendance ratio';
            $subject = 'Followup';
            $address = "http://10.216.2.202/hrar_notifier/notify_initial.php";
            $series = $approval->series;
            $approvalSeriesId = $series . str_replace(" ", "_", $approval->division);

            $division = $approval->division;

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
                    .  implode("&", $ncflJsonQueryString)
                    . "&" .  implode("&", $npflJsonQueryString)
            );
        }
    }
}
