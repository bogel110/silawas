<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // Membuka gembok keamanan agar semua kolom bisa diisi, KECUALI kolom 'id'
    protected $guarded = ['id'];

    // Relasi balik ke model School (opsional tapi sangat disarankan)
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}