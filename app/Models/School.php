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
    public function kbmReports()
    {
        // hasMany berarti "Satu sekolah memiliki BANYAK laporan KBM"
        // orderBy digunakan agar tahun pelajaran terbaru selalu muncul di atas
        return $this->hasMany(KbmReport::class)->orderBy('tahun_pelajaran', 'desc');
    }
    public function getSkorPerformaAttribute()
    {
        $filledLinks = 0;
        
        // Cek 9 kolom dokumen, jika ada isinya maka poin bertambah
        if (!empty($this->ijop_link)) $filledLinks++;
        if (!empty($this->ksp_link)) $filledLinks++;
        if (!empty($this->akreditasi_link)) $filledLinks++;
        if (!empty($this->gtk_link)) $filledLinks++;
        if (!empty($this->pd_link)) $filledLinks++;
        if (!empty($this->sarpras_link)) $filledLinks++;
        if (!empty($this->rapor_link)) $filledLinks++;
        if (!empty($this->rkt_link)) $filledLinks++;
        if (!empty($this->rkas_link)) $filledLinks++;

        // Hitung persentase dan bulatkan angkanya (0 - 100)
        return round(($filledLinks / 9) * 100, 2);
    }
}