<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    use HasFactory;

    // PASTIKAN 'tahun_pelajaran' ADA DI DALAM SINI
    protected $fillable = [
        'school_id', 
        'bulan', 
        'tahun',
        'tahun_pelajaran', // <--- Tambahan baru
        'kurikulum_link', 
        'kesiswaan_link', 
        'sarpras_link', 
        'humas_link'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}