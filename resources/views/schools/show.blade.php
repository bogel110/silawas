@extends('layouts.app')

@section('title', 'Detail: ' . $school->name)

@section('content')
    <div class="mb-4">
        <a href="{{ url('/') }}" class="text-decoration-none text-muted fw-bold small d-flex align-items-center gap-1 mb-3">
            <span class="material-symbols-outlined fs-6">arrow_back</span> Kembali ke Dashboard
        </a>
        <div class="d-flex justify-content-between align-items-end">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold mb-2">{{ $school->level }} • {{ $school->status }}</span>
                <div class="d-flex align-items-center gap-3">
                    <h2 class="display-6 fw-extrabold font-headline mb-0">{{ $school->name }}</h2>
                </div>
            </div>
            <div class="text-end">
                <p class="text-muted small mb-1 fw-bold text-uppercase tracking-wider">Progress</p>
                <h3 class="fw-bold text-primary mb-0">{{ number_format($school->score, 1) }}%</h3>
            </div>
            {{-- <div class="text-end">
                <p class="text-muted small mb-1 fw-bold text-uppercase tracking-wider">Composite Score</p>
                <h3 class="fw-bold text-primary mb-0">{{ number_format($school->score, 1) }}%</h3>
            </div> --}}
        </div>
    </div>
    @if($errors->any())
                        <div class="px-4 pt-3">
                            <div class="alert alert-danger alert-dismissible fade show py-2 small mb-0" role="alert">
                                <strong>Gagal menyimpan!</strong>
                                <ul class="mb-0 ps-3 mt-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="px-4 pt-3">
                            <div class="alert alert-success alert-dismissible fade show py-2 small mb-0" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    @endif
    <div class="row g-4 d-flex justify-content-between align-items-end">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="font-headline fw-bold mb-0">1. Berkas Administrasi</h5>
                </div>
                <div class="card-body p-4">
                    @if(auth()->user()->role === 'admin_sekolah' && auth()->user()->school_id == $school->id)
                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold d-flex align-items-center gap-1 mt-2" data-bs-toggle="modal" data-bs-target="#modalDokumenMaster">
                            <span class="material-symbols-outlined fs-6">edit</span> Input Link Dokumen
                        </button>
                    @endif
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="small fw-medium">1. Izin Operasional (IJOP)</span>
                            @if($school->ijop_link) <a href="{{ $school->ijop_link }}" target="_blank" class="badge bg-success text-decoration-none">Lihat Berkas</a>
                            @else <span class="badge bg-danger">Kosong</span> @endif
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="small fw-medium">2. Kurikulum Satuan Pendidikan (KSP)</span>
                            @if($school->ksp_link) <a href="{{ $school->ksp_link }}" target="_blank" class="badge bg-success text-decoration-none small">Lihat Berkas</a>
                            @else <span class="badge bg-danger">Kosong</span> @endif
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="small fw-medium">3. Sertifikat Akreditasi</span>
                            @if($school->akreditasi_link) <a href="{{ $school->akreditasi_link }}" target="_blank" class="badge bg-success text-decoration-none small">Lihat Berkas</a>
                            @else <span class="badge bg-danger">Kosong</span> @endif
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="small fw-medium">4. Data GTK</span>
                            @if($school->gtk_link) <a href="{{ $school->gtk_link }}" target="_blank" class="badge bg-success text-decoration-none">Lihat Berkas</a>
                            @else <span class="badge bg-danger">Kosong</span> @endif
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="small fw-medium">5. Data Peserta Didik</span>
                            @if($school->pd_link) <a href="{{ $school->pd_link }}" target="_blank" class="badge bg-success text-decoration-none">Lihat Berkas</a>
                            @else <span class="badge bg-danger">Kosong</span> @endif
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-bottom-0">
                            <span class="small fw-medium">6. Data SARPRAS</span>
                            @if($school->sarpras_link) <a href="{{ $school->sarpras_link }}" target="_blank" class="badge bg-success text-decoration-none">Lihat Berkas</a>
                            @else <span class="badge bg-danger">Kosong</span> @endif
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-bottom-0">
                            <span class="small fw-medium">7. Rapor Pendidikan</span>
                            @if($school->rapor_link) <a href="{{ $school->rapor_link }}" target="_blank" class="badge bg-success text-decoration-none small">Lihat Berkas</a>
                            @else <span class="badge bg-danger">Kosong</span> @endif
                        </li>
                        <hr class="my-2 opacity-25"> 
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-bottom-0">
                            <span class="small fw-medium">8. RPP / Modul Ajar</span>
                                @if($school->rpp_link) <a href="{{ $school->rpp_link }}" target="_blank" class="badge bg-success text-decoration-none small">Lihat Berkas</a>
                                @else <span class="badge bg-danger">Kosong</span> @endif
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-bottom-0">
                            <span class="small fw-medium">9. Dokumentasi Ekskul & P5</span>
                                @if($school->ekskul_link) <a href="{{ $school->ekskul_link }}" target="_blank" class="badge bg-success text-decoration-none small">Lihat Berkas</a>
                                @else <span class="badge bg-danger">Kosong</span> @endif
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-4">
                <span class="small text-muted fw-bold d-block mb-2">Catatan/Rekomendasi Pengawas</span>
    
                    @if(auth()->user()->role === 'pengawas')
                        {{-- Tampilan untuk Pengawas: Bisa Edit --}}
                        <form action="{{ route('school.update_catatan', $school->id) }}" method="POST">
                            @csrf
                            <textarea name="catatan_pengawas" class="form-control border-0 shadow-sm mb-2" rows="4" placeholder="Tulis rekomendasi dan hasil evaluasi pengawasan di sini...">{{ $school->catatan_pengawas }}</textarea>
                            <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">Simpan Evaluasi</button>
                        </form>
        
                    @else
                        {{-- Tampilan untuk Admin Sekolah & Role lain: Hanya Baca --}}
                        <div class="p-3 bg-white rounded-3 border shadow-sm small">
                            @if($school->catatan_pengawas)
                                {!! nl2br(e($school->catatan_pengawas)) !!}
                            @else
                                <span class="text-muted fst-italic">Belum ada catatan evaluasi dari Pengawas.</span>
                            @endif
                        </div>
                    @endif
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="font-headline fw-bold mb-0">2. Kontrol KBM</h5>
                    @if(auth()->user()->role === 'admin_sekolah' && auth()->user()->school_id == $school->id)
                                <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalAbsensi">
                                    + Isi Absensi Hari Ini
                                </button>
                            @else
                                {{-- <span class="badge bg-light text-dark border">Data Kehadiran</span> --}}
                            @endif
                    {{-- <span class="badge bg-light text-dark border">Data Kehadiran & Perangkat</span> --}}
                </div>
                <div class="card-body p-4">
                    <h6 class="fw-bold small text-muted text-uppercase tracking-wider mb-3">Rekap Kehadiran Terakhir</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th>Tanggal</th>
                                    <th class="text-center">Siswa Hadir</th>
                                    <th class="text-center">Guru Hadir</th>
                                    <th class="text-center">Kepsek Hadir</th>
                                    <th class="text-center">Aksi</th> </tr>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($school->attendances->take(5) as $absen)
                                <tr>
                                    <td class="small">{{ $absen->tanggal }}</td>
                                    <td class="text-center small">{{ $absen->siswa_hadir }}</td>
                                    <td class="text-center small">{{ $absen->guru_hadir }}</td>
                                    <td class="text-center"><span class="material-symbols-outlined fs-6 {{ $absen->kepsek_hadir ? 'text-success' : 'text-danger' }}">{{ $absen->kepsek_hadir ? 'check_circle' : 'cancel' }}</span></td>
                                    </td>
                                    <td class="text-center">
                                        @if(auth()->user()->role === 'pengawas' || auth()->user()->school_id == $school->id)
                                        <form action="{{ route('attendance.destroy', $absen->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0">
                                                <span class="material-symbols-outlined fs-6">delete</span>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center small text-muted py-3">Belum ada data absensi harian.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     {{-- <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <span class="small text-muted fw-bold d-block mb-2">Folder RPP / Modul Ajar</span>
                                @if($school->rpp_link) <a href="{{ $school->rpp_link }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">Lihat Berkas</a>
                                @else <span class="text-danger small fw-bold">Belum ada tautan</span> @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <span class="small text-muted fw-bold d-block mb-2">Dokumentasi Ekskul & P5</span>
                                @if($school->ekskul_link) <a href="{{ $school->ekskul_link }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">Lihat Berkas</a>
                                @else <span class="text-danger small fw-bold">Belum ada tautan</span> @endif
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="font-headline fw-bold mb-0">3. Laporan Kinerja Wakasek</h5>
                </div>
                <div class="card-body p-4">
                    @if(auth()->user()->role === 'admin_sekolah' && auth()->user()->school_id == $school->id)
                                <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalLaporan">
                                + Tambah Laporan Bulanan
                                 </button>
                    @endif
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th>Bulan</th>
                                    <th>Tahun Pelajaran</th> <th>Kurikulum</th>
                                    <th>Kesiswaan</th>
                                    <th>Sarpras</th>
                                    <th>Humas</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($school->monthlyReports as $report)
                                <tr>
                                    <td class="fw-bold small text-dark">{{ \Carbon\Carbon::create()->month($report->bulan)->translatedFormat('F') }}</td>
                                    <td class="small text-muted fw-bold">{{ $report->tahun_pelajaran ?? '-' }}</td> <td>@if($report->kurikulum_link) <a href="{{ $report->kurikulum_link }}" target="_blank" class="text-decoration-none">Cek Berkas</a> @else - @endif</td>
                                    <td>@if($report->kesiswaan_link) <a href="{{ $report->kesiswaan_link }}" target="_blank" class="text-decoration-none">Cek Berkas</a> @else - @endif</td>
                                    <td>@if($report->sarpras_link) <a href="{{ $report->sarpras_link }}" target="_blank" class="text-decoration-none">Cek Berkas</a> @else - @endif</td>
                                    <td>@if($report->humas_link) <a href="{{ $report->humas_link }}" target="_blank" class="text-decoration-none">Cek Berkas</a> @else - @endif</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-link text-primary p-0" data-bs-toggle="modal" data-bs-target="#editModalLaporan{{ $report->id }}">
                                                <span class="material-symbols-outlined fs-6">edit</span>
                                            </button>
                                            <form action="{{ route('school.destroy_monthly_report', $report->id) }}" method="POST" onsubmit="return confirm('Hapus laporan bulan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0">
                                                    <span class="material-symbols-outlined fs-6">delete</span>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="modal fade" id="editModalLaporan{{ $report->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow rounded-4 text-start">
                                                    <div class="modal-header border-bottom-0">
                                                        <h1 class="modal-title fs-6 font-headline fw-bold">Edit Laporan: {{ \Carbon\Carbon::create()->month($report->bulan)->translatedFormat('F') }}</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('school.update_monthly_report', $report->id) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body py-0">
                                                            <div class="mb-3">
                                                                <label class="small fw-bold">Tahun Pelajaran</label>
                                                                <input type="text" name="tahun_pelajaran" class="form-control form-control-sm" value="{{ $report->tahun_pelajaran }}" placeholder="Contoh: 2023/2024" required>
                                                            </div>
                                                            
                                                            <div class="mb-2">
                                                                <label class="small fw-bold">Link Kurikulum</label>
                                                                <input type="text" name="kurikulum_link" class="form-control form-control-sm" value="{{ $report->kurikulum_link }}">
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="small fw-bold">Link Kesiswaan</label>
                                                                <input type="text" name="kesiswaan_link" class="form-control form-control-sm" value="{{ $report->kesiswaan_link }}">
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="small fw-bold">Link Sarpras</label>
                                                                <input type="text" name="sarpras_link" class="form-control form-control-sm" value="{{ $report->sarpras_link }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="small fw-bold">Link Humas</label>
                                                                <input type="text" name="humas_link" class="form-control form-control-sm" value="{{ $report->humas_link }}">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-top-0">
                                                            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center small text-muted py-4">Laporan bulanan belum tersedia.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAbsensi" tabindex="-1" aria-labelledby="modalAbsensiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0">
                <h1 class="modal-title fs-5 font-headline fw-bold" id="modalAbsensiLabel">Input Kehadiran Harian</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('school.store_attendance', $school->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4 pt-0">
                    <p class="small text-muted mb-4">Silakan masukkan data kehadiran yang valid untuk hari ini ({{ now()->format('d M Y') }}).</p>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jumlah Siswa Hadir</label>
                        <input type="number" name="siswa_hadir" class="form-control" required min="0" placeholder="Contoh: 345">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jumlah Guru Hadir</label>
                        <input type="number" name="guru_hadir" class="form-control" required min="0" placeholder="Contoh: 42">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kehadiran Kepala Sekolah</label>
                        <select name="kepsek_hadir" class="form-select" required>
                            <option value="1">Hadir (Ada di tempat)</option>
                            <option value="0">Tidak Hadir (Dinas Luar / Izin)</option>
                        </select>
                    </div>f
                </div>
                <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalLaporan" tabindex="-1" aria-labelledby="modalLaporanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0">
                <h1 class="modal-title fs-5 font-headline fw-bold" id="modalLaporanLabel">Input Laporan Bulanan Wakasek</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('school.store_monthly_report', $school->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4 pt-0">
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary">Tahun Pelajaran</label>
                            <select name="tahun_pelajaran" class="form-select bg-light" required>
                                <option value="">-- Pilih Tahun --</option>
                                @php $currentYear = date('Y'); @endphp
                                @for($i = $currentYear - 1; $i <= $currentYear + 1; $i++)
                                    <option value="{{ $i }}/{{ $i+1 }}">{{ $i }}/{{ $i+1 }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary">Pilih Bulan Laporan</label>
                            <select name="bulan" class="form-select bg-light" required>
                                <option value="">-- Pilih Bulan --</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                    </div>
                    
                    <p class="small text-muted mb-3 border-bottom pb-2">Masukkan Tautan (Link) Google Drive untuk masing-masing bidang. Kosongkan jika belum ada.</p>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link Laporan Kurikulum</label>
                            <input type="text" name="kurikulum_link" class="form-control" placeholder="https://drive.google.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link Laporan Kesiswaan</label>
                            <input type="text" name="kesiswaan_link" class="form-control" placeholder="https://drive.google.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link Laporan Sarpras</label>
                            <input type="text" name="sarpras_link" class="form-control" placeholder="https://drive.google.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link Laporan Humas</label>
                            <input type="text" name="humas_link" class="form-control" placeholder="https://drive.google.com/...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Simpan Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalDokumenMaster" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0">
                <h1 class="modal-title fs-5 font-headline fw-bold">Update Link Dokumen Master</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('school.update_links', $school->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4 pt-0">
                    <p class="small text-muted mb-4">Masukkan tautan (link) Google Drive untuk memperbarui data kelengkapan administrasi sekolah.</p>
                    
                    <h6 class="fw-bold small text-primary border-bottom pb-2 mb-3">Modul 1: Administrasi</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link Izin Operasional (IJOP)</label>
                            <input type="url" name="ijop_link" class="form-control" value="{{ $school->ijop_link }}" placeholder="https://drive.google.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link KSP (Kurikulum)</label>
                            <input type="url" name="ksp_link" class="form-control form-control-sm" value="{{ $school->ksp_link }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link Sertifikat Akreditasi</label>
                            <input type="url" name="akreditasi_link" class="form-control form-control-sm" value="{{ $school->akreditasi_link }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link Data GTK</label>
                            <input type="url" name="gtk_link" class="form-control form-control-sm" value="{{ $school->gtk_link }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link Data GTK</label>
                            <input type="url" name="gtk_link" class="form-control" value="{{ $school->gtk_link }}" placeholder="https://drive.google.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link Data Peserta Didik</label>
                            <input type="url" name="pd_link" class="form-control" value="{{ $school->pd_link }}" placeholder="https://drive.google.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link Data SARPRAS</label>
                            <input type="url" name="sarpras_link" class="form-control" value="{{ $school->sarpras_link }}" placeholder="https://drive.google.com/...">
                        </div>
                    </div>

                    <h6 class="fw-bold small text-primary border-bottom pb-2 mb-3">Modul 2 & 4: KBM dan Rapor</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link RPP / Modul Ajar</label>
                            <input type="url" name="rpp_link" class="form-control" value="{{ $school->rpp_link }}" placeholder="https://drive.google.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link Dokumentasi Ekskul</label>
                            <input type="url" name="ekskul_link" class="form-control" value="{{ $school->ekskul_link }}" placeholder="https://drive.google.com/...">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Link Rapor Pendidikan</label>
                            <input type="url" name="rapor_link" class="form-control" value="{{ $school->rapor_link }}" placeholder="https://drive.google.com/...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection