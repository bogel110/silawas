<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Mengizinkan pengisian semua kolom kecuali ID

    // Relasi ke tabel Attendances (Modul 2)
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Relasi ke tabel Monthly Reports (Modul 3)
    public function monthlyReports()
    {
        return $this->hasMany(MonthlyReport::class);
    }

    public function school()
    {
        // Format: belongsTo(ModelTujuan, KolomDiUser, KolomDiTujuan)
        return $this->belongsTo(School::class, 'school_name', 'name');
    }
}