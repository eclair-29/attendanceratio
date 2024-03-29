<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffBaseDetail extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'staff_code', 'staff_code');
    }
}
