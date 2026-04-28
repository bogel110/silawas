<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $this->authorizePengawas();

        $users = User::latest()->get();
        $schools = School::orderBy('name', 'asc')->get(); 
        
        return view('admin.users.index', compact('users', 'schools'));
    }

    public function store(Request $request)
    {
        $this->authorizePengawas();

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8',
            'role'      => 'required|in:pengawas,admin_sekolah',
        ]);

        $school_id = null;

        if ($request->role !== 'pengawas') {
            $request->validate([
                'school_name'   => 'required|string|max:255',
                'school_level'  => 'required|string',
                'school_status' => 'required|string',
            ]);

            $school = School::firstOrCreate(
                ['name' => $request->school_name],
                [
                    'level' => $request->school_level,
                    'status' => $request->school_status
                ]
            );
            $school_id = $school->id;
        }

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'school_id' => $school_id,
        ]);

        $pesan = $request->role === 'pengawas' 
                 ? 'Akun Pengawas berhasil ditambahkan!' 
                 : 'Akun Admin dan data Sekolah berhasil ditambahkan!';

        return redirect()->back()->with('success', $pesan);
    }

    // ==========================================
    // FUNGSI BARU: UPDATE DATA USER
    // ==========================================
    public function update(Request $request, $id)
    {
        $this->authorizePengawas();

        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id, // Abaikan email milik dia sendiri
            'role'  => 'required|in:pengawas,admin_sekolah',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // Atur ID Sekolah berdasarkan Role
        if ($request->role === 'pengawas') {
            $user->school_id = null; // Pengawas tidak terikat 1 sekolah
        } else {
            $request->validate([
                'school_id' => 'required|exists:schools,id',
            ]);

            $user->school_id = $request->school_id;
        }

        $user->save();

        return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->authorizePengawas();

        if ((int) auth()->id() === (int) $id) {
            return back()->withErrors(['user' => 'Anda tidak dapat menghapus akun yang sedang digunakan.']);
        }
        
        User::findOrFail($id)->delete();
        return back()->with('success', 'Akun berhasil dihapus!');
    }
    
    public function resetPassword(Request $request, $id)
    {
        $this->authorizePengawas();

        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password untuk akun ' . $user->name . ' berhasil direset!');
    }
}
