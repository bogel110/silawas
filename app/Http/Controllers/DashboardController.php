<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Jika yang login adalah Admin Sekolah, langsung arahkan ke sekolahnya
        if ($user->role === 'admin_sekolah') {
            return redirect()->route('school.show', $user->school_id);
        }

        // 2. Jika yang login adalah Pengawas, tampilkan Dashboard Utama
        $totalSchools = School::count();

        $schools = School::all()->map(function ($school) {
            $filledLinks = 0;
            if ($school->ijop_link) $filledLinks++;
            if ($school->ksp_link) $filledLinks++;
            if ($school->akreditasi_link) $filledLinks++;
            if ($school->gtk_link) $filledLinks++;
            if ($school->pd_link) $filledLinks++;
            if ($school->sarpras_link) $filledLinks++;
            if ($school->rapor_link) $filledLinks++;
            if ($school->rkt_link) $filledLinks++;
            if ($school->rkas_link) $filledLinks++;

            $school->score = ($filledLinks / 9) * 100;

            if ($school->score >= 85) {
                $school->status_text = 'Berkas Lengkap';
                $school->status_color = 'success';
            } elseif ($school->score >= 60) {
                $school->status_text = 'Beberapa Berkas Tidak Lengkap';
                $school->status_color = 'primary';
            } elseif ($school->score > 0) {
                $school->status_text = 'Berkas Kurang Lengkap';
                $school->status_color = 'warning';
            } else {
                $school->status_text = 'Tidak Mengisi';
                $school->status_color = 'danger';
            }

            return $school;
        })->sortByDesc('score');

        $avgCompletion = $totalSchools > 0 ? $schools->avg('score') : 0;

        return view('dashboard', compact('totalSchools', 'schools', 'avgCompletion'));
    }
}