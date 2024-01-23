<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuPerDivsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = Carbon::now();
        $buPerDivs = [
            ['div_head' => 'miguel.dechavez@nidec.com', 'division' => 'PRODUCTION DEVELOPMENT CENTER', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => 'miguel.dechavez@nidec.com', 'division' => 'ACCOUNTING AND FINANCE', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => 'miguel.dechavez@nidec.com', 'division' => 'ADMINISTRATION', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => 'miguel.dechavez@nidec.com', 'division' => 'BCO', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => 'miguel.dechavez@nidec.com', 'division' => 'CENTRAL PROCUREMENT', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => 'miguel.dechavez@nidec.com', 'division' => 'FACILITIES PLANNING AND OPERATION', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => 'miguel.dechavez@nidec.com', 'division' => 'OFFICE OF THE PRESIDENT', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => 'miguel.dechavez@nidec.com', 'division' => 'PARTS', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => 'miguel.dechavez@nidec.com', 'division' => 'PRODUCTION', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => 'miguel.dechavez@nidec.com', 'division' => 'PRODUCTION PLANNING', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => 'miguel.dechavez@nidec.com', 'division' => 'QUALITY ASSURANCE', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => 'miguel.dechavez@nidec.com', 'division' => 'SPM', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => 'miguel.dechavez@nidec.com', 'division' => 'SPS-2', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => 'miguel.dechavez@nidec.com', 'division' => 'TECHNICAL SUPPORT', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => 'miguel.dechavez@nidec.com', 'division' => 'WAREHOUSE MANAGEMENT', 'created_at' => $date, 'updated_at' => $date],
        ];

        foreach ($buPerDivs as $buPerDiv) {
            DB::table('bu_per_divs')->insert($buPerDiv);
        }
    }
}
