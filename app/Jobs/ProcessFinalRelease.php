<?php

namespace App\Jobs;

use App\Models\ApprovalPerDiv;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessFinalRelease implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $overallNcfl;
    public $overall;
    public $to;
    public $series;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($overallNcfl, $overall, $to, $series)
    {
        $this->overallNcfl = $overallNcfl;
        $this->overall = $overall;
        $this->to = $to;
        $this->series = $series;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $postdata = http_build_query(
            array(
                'overallNcfl' => $this->overallNcfl,
                'overall' => $this->overall,
                'to' => $this->to
            )
        );

        $opts = array(
            'http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );

        $address = "http://10.216.2.202/hrar_notifier/notify_final.php";

        ApprovalPerDiv::where('series', $this->series)->update(['status' => 'approved', 'is_expired' => 'yes']);

        $context  = stream_context_create($opts);
        file_get_contents($address . "?series=" . $this->series, false, $context);
    }
}
