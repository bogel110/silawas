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

        // Ambil data user admin sekolah. 
        // (Kita hapus with('school') karena sekarang pakai input teks manual)
        $users = User::where('role', 'admin_sekolah')->latest()->get();
        
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
{
    // Validasi
    $request->validate([
        'name'          => 'required|string|max:255',
        'email'         => 'required|email|unique:users,email',
        'password'      => 'required|string|min:8',
        'role'         => 'required|string',
        'school_name'   => 'required|string|max:255',
        'school_level'  => 'required|string',
        'school_status' => 'required|string',
    ]);

    // 1. Simpan ke Database Schools (Mencegah duplikat jika nama sekolah sama)
    $school = \App\Models\School::firstOrCreate(
        ['name' => $request->school_name],
        [
            'level' => $request->school_level,
            'status' => $request->school_status
        ]
    );

    // 2. Simpan ke Database Users
    \App\Models\User::create([
        'name'      => $request->name,
        'email'     => $request->email,
        'password'  => \Illuminate\Support\Facades\Hash::make($request->password),
        'role'      => $request->role,
        'school_id' => $school->id, // Ambil ID dari tabel schools yang baru saja dibuat
    ]);

    return redirect()->back()->with('success', 'Akun admin dan data sekolah berhasil ditambahkan!');
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