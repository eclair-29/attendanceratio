<?php

namespace App\Rules;

use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\InvokableRule;

class FileNotMatch implements InvokableRule
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
        $fileLabel = $value->getClientOriginalName();

        if (!Str::contains($fileLabel, ['PA', 'PR', 'JT', 'HAS', 'NC', 'VAC'])) $fail('File name should contain prefix PA (for Allsec) or PR (For Agency Attendance)');
    }
}
