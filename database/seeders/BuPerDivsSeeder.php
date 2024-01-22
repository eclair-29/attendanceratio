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
            ['div_head' => '', 'division' => 'ACCOUNTING AND FINANCE', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => '', 'division' => 'ADMINISTRATION', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => '', 'division' => 'BCO', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => '', 'division' => 'CENTRAL PROCUREMENT', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => '', 'division' => 'FACILITIES PLANNING AND OPERATION', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => '', 'division' => 'OFFICE OF THE PRESIDENT', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => '', 'division' => 'PARTS', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => '', 'division' => 'PRODUCTION', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => '', 'division' => 'PRODUCTION PLANNING', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => '', 'division' => 'QUALITY ASSURANCE', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => '', 'division' => 'SPM', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => '', 'division' => 'SPS-2', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => '', 'division' => 'TECHNICAL SUPPORT', 'created_at' => $date, 'updated_at' => $date],
            ['div_head' => '', 'division' => 'WAREHOUSE MANAGEMENT', 'created_at' => $date, 'updated_at' => $date],
        ];

        foreach ($buPerDivs as $buPerDiv) {
            DB::table('bu_per_divs')->insert($buPerDiv);
        }
    }
}
