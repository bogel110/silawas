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
        if (auth()->user()->role !== 'pengawas') abort(403);

        // VALIDASI: Meminta school_name (bukan school_id)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'school_name' => 'required|string|max:255', 
        ]);

        // PROSES SIMPAN: Menyimpan school_name (bukan school_id)
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin_sekolah',
            'school_name' => $request->school_name, 
        ]);

        return back()->with('success', 'Akun Admin Sekolah berhasil dibuat!');
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