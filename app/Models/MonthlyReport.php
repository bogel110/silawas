<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    use HasFactory;

    // Buka gembok keamanan
    protected $guarded = ['id'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}