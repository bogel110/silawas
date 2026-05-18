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
        return $this->hasMany(KbmReport::class)->orderBy('tahun_pelajaran', 'desc');
    }

    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'pengawas_school', 'school_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Menghitung skor performa sekolah berdasarkan kelengkapan 10 link dokumen utama.
     */
    public function getSkorPerformaAttribute()
    {
        $links = [
            'ijop_link', 'ksp_link', 'akreditasi_link', 'gtk_link', 'pd_link',
            'sarpras_link', 'rapor_link', 'rkt_link', 'rkas_link', 'contact_link'
        ];

        $filledLinks = 0;
        foreach ($links as $link) {
            if (!empty($this->$link)) {
                $filledLinks++;
            }
        }

        return round(($filledLinks / count($links)) * 100, 2);
    }

    /**
     * Mendapatkan label status berdasarkan skor performa.
     */
    public function getStatusLabelAttribute()
    {
        $score = $this->skor_performa;

        if ($score >= 85) {
            return 'Berkas Lengkap';
        } elseif ($score >= 60) {
            return 'Beberapa Berkas Tidak Lengkap';
        } elseif ($score > 0) {
            return 'Berkas Kurang Lengkap';
        } else {
            return 'Tidak Mengisi';
        }
    }

    /**
     * Mendapatkan warna status (Bootstrap/Tailwind context) berdasarkan skor performa.
     */
    public function getStatusColorAttribute()
    {
        $score = $this->skor_performa;

        if ($score >= 85) {
            return 'success';
        } elseif ($score >= 60) {
            return 'primary';
        } elseif ($score > 0) {
            return 'warning';
        } else {
            return 'danger';
        }
    }
}
