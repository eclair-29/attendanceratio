<?php

use App\Models\Attendance;
use App\Models\BatchTracker;

function clearQueueTables($batchType)
{
    if ($batchType == 'attendance') {
        if (Attendance::count() != 0) Attendance::truncate();
    };

    $batchTrack = BatchTracker::where('type', $batchType)->first();
    if ($batchTrack) $batchTrack->delete();
}
