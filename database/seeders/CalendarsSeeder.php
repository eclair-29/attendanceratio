<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalendarsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = Carbon::now();
        $calendars = [
            ['day_count' => 18, 'month' => 12, 'year' => 2023, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],

            ['day_count' => 21, 'month' => 12, 'year' => 2023, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],

            ['day_count' => 22, 'month' => 1, 'year' => 2024, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 19, 'month' => 2, 'year' => 2024, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 21, 'month' => 3, 'year' => 2024, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 20, 'month' => 4, 'year' => 2024, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 22, 'month' => 5, 'year' => 2024, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 17, 'month' => 6, 'year' => 2024, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 23, 'month' => 7, 'year' => 2024, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 20, 'month' => 8, 'year' => 2024, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 21, 'month' => 9, 'year' => 2024, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],

            ['day_count' => 23, 'month' => 10, 'year' => 2024, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 20, 'month' => 11, 'year' => 2024, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 18, 'month' => 12, 'year' => 2024, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],

            ['day_count' => 22, 'month' => 1, 'year' => 2025, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 19, 'month' => 2, 'year' => 2025, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 21, 'month' => 3, 'year' => 2025, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],

            ['day_count' => 26, 'month' => 1, 'year' => 2024, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 22, 'month' => 2, 'year' => 2024, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 25, 'month' => 3, 'year' => 2024, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 24, 'month' => 4, 'year' => 2024, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 26, 'month' => 5, 'year' => 2024, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 22, 'month' => 6, 'year' => 2024, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 27, 'month' => 7, 'year' => 2024, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 25, 'month' => 8, 'year' => 2024, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 25, 'month' => 9, 'year' => 2024, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],

            ['day_count' => 27, 'month' => 10, 'year' => 2024, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 23, 'month' => 11, 'year' => 2024, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 21, 'month' => 12, 'year' => 2024, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],

            ['day_count' => 26, 'month' => 1, 'year' => 2025, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 22, 'month' => 2, 'year' => 2025, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],
            ['day_count' => 25, 'month' => 3, 'year' => 2025, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],

            ['day_count' => 23, 'month' => 11, 'year' => 2023, 'shift_type' => 'shifting', 'created_at' => $date, 'updated_at' => $date],

            ['day_count' => 20, 'month' => 11, 'year' => 2023, 'shift_type' => 'compressed', 'created_at' => $date, 'updated_at' => $date],
        ];

        foreach ($calendars as $calendar) {
            DB::table('calendars')->insert($calendar);
        }
    }
}
