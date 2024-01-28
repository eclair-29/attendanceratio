<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessFinalRelease implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $overallNcfl;
    public $overallNpfl;
    public $series;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($overallNcfl, $overallNpfl, $series)
    {
        $this->overallNcfl = $overallNcfl;
        $this->overallNpfl = $overallNpfl;
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
                'overallNpfl' => $this->overallNpfl
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

        $context  = stream_context_create($opts);
        file_get_contents($address . "?series=" . $this->series, false, $context);
    }
}
