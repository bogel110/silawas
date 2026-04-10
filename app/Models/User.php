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
        'role',
        'school_name', // <--- Ini yang mengizinkan teks manual masuk ke database
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
    
    // Fungsi public function school() sudah dihapus 
    // karena kita tidak lagi menggunakan ID, melainkan Teks Manual.
}