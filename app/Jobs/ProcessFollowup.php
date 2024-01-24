<?php

namespace App\Jobs;

use App\Models\ApprovalPerDiv;
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
            $division = $approval->division;
            $seriesId = $approval->series_id;
            $notifMsg = 'For followup';
            $subject = 'Followup';
            return notifyInitial($division, $seriesId, $notifMsg, $subject);
        }
    }
}
