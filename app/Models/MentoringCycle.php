<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentoringCycle extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke tabel sekolah
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}