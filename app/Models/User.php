<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 1. GUNAKAN ARRAY KLASIK INI (Hapus #[Fillable] di atas)
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',       // <--- SAYA UBAH DARI 'role' MENJADI 'level' AGAR SESUAI DENGAN FORM
        'school_name', // (Opsional, dibiarkan jika masih butuh backup teks manual)
        'school_id',   // <--- PASTIKAN INI ADA UNTUK MENYIMPAN ID
    ];

    // 2. GUNAKAN ARRAY KLASIK INI (Hapus #[Hidden] di atas)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    // 3. KEMBALIKAN FUNGSI INI AGAR BISA MENGAMBIL NAMA SEKOLAH DI BLADE
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function supervisedSchools()
    {
        return $this->belongsToMany(School::class, 'pengawas_school', 'user_id', 'school_id')
            ->withTimestamps();
    }
}
