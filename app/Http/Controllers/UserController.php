<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Proteksi: Hanya pengawas yang boleh akses halaman ini
        if (auth()->user()->role !== 'pengawas') {
            abort(403, 'Akses Ditolak. Halaman ini khusus Pengawas.');
        }

        // UBAHAN: Hapus filter 'admin_sekolah' agar akun Pengawas juga tampil di tabel
        $users = User::latest()->get();
        
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Akun Dasar (Berlaku untuk SEMUA tipe user)
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8',
            'role'      => 'required|string',
        ]);

        $school_id = null; // Default null untuk pengawas

        // 2. Validasi & Simpan Data Sekolah (HANYA JIKA BUKAN PENGAWAS)
        if ($request->role !== 'pengawas') {
            $request->validate([
                'school_name'   => 'required|string|max:255',
                'school_level'  => 'required|string',
                'school_status' => 'required|string',
            ]);

            // Simpan ke Database Schools (Mencegah duplikat jika nama sekolah sama)
            $school = \App\Models\School::firstOrCreate(
                ['name' => $request->school_name],
                [
                    'level' => $request->school_level,
                    'status' => $request->school_status
                ]
            );
            
            // Ambil ID sekolah yang baru dibuat/ditemukan
            $school_id = $school->id;
        }

        // 3. Simpan ke Database Users
        \App\Models\User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'school_id' => $school_id, // Berisi angka untuk admin sekolah, berisi NULL untuk pengawas
        ]);

        // Pesan sukses dinamis
        $pesan = $request->role === 'pengawas' 
                 ? 'Akun Pengawas berhasil ditambahkan!' 
                 : 'Akun Admin dan data Sekolah berhasil ditambahkan!';

        return redirect()->back()->with('success', $pesan);
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'pengawas') abort(403);
        
        User::findOrFail($id)->delete();
        return back()->with('success', 'Akun berhasil dihapus!');
    }
    
    public function resetPassword(Request $request, $id)
    {
        // Pastikan hanya pengawas yang bisa akses
        if (auth()->user()->role !== 'pengawas') {
            abort(403);
        }

        // Validasi password baru
        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        // Cari user dan perbarui passwordnya
        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password untuk akun ' . $user->name . ' berhasil direset!');
    }
}