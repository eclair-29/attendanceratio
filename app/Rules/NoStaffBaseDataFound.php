<?php

namespace App\Rules;

use App\Models\StaffBaseDetail;
use Illuminate\Contracts\Validation\InvokableRule;

class NoStaffBaseDataFound implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        $staffBaseDataCount = StaffBaseDetail::count();

        if ($staffBaseDataCount === 0) $fail('No staff base data found. Please Upload Master File first.');
    }
}
