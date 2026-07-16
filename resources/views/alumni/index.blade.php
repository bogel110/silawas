@extends('layouts.app')

@section('title', 'Peta Alumni')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div>
                <h1 class="h3 font-headline fw-bold mb-1">Peta Alumni</h1>
                <p class="text-soft mb-0">Kelola data alumni sekolah Anda</p>
            </div>
            <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalAlumni" type="button">
                <span class="material-symbols-outlined fs-6">add</span>
                <span>Tambah Alumni</span>
            </button>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="fw-bold mb-2">Terjadi kesalahan:</div>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('import_errors'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="fw-bold mb-2">Beberapa baris gagal diimpor:</div>
        <ul class="mb-0 small">
            @foreach(session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Statistik Alumni Cards -->
<div class="row g-3 mb-4">
    <!-- Stat Card 1: Total Alumni -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #0F6B7D 0%, #138B9C 100%);">
                <span class="material-symbols-outlined">people</span>
            </div>
            <div class="stat-content">
                <p class="stat-label">Total Alumni</p>
                <h4 class="stat-value">{{ $stats['total'] }}</h4>
            </div>
        </div>
    </div>

    <!-- Stat Card 2: Melanjutkan Studi -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #0D84E8 0%, #0B9FE8 100%);">
                <span class="material-symbols-outlined">school</span>
            </div>
            <div class="stat-content">
                <p class="stat-label">Melanjutkan Studi</p>
                <h4 class="stat-value">{{ $stats['melanjutkan_studi'] }}</h4>
            </div>
        </div>
    </div>

    <!-- Stat Card 3: Bekerja -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #28A745 0%, #2BCA5C 100%);">
                <span class="material-symbols-outlined">work</span>
            </div>
            <div class="stat-content">
                <p class="stat-label">Bekerja</p>
                <h4 class="stat-value">{{ $stats['bekerja'] }}</h4>
            </div>
        </div>
    </div>

    <!-- Stat Card 4: Data Lengkap -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #FF9800 0%, #FFB74D 100%);">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
            <div class="stat-content">
                <p class="stat-label">Kelengkapan</p>
                <h4 class="stat-value">{{ $stats['total'] > 0 ? round(100) : 0 }}%</h4>
            </div>
        </div>
    </div>
</div>

<!-- Breakdown Statistik - Pie Charts -->
<div class="row g-3 mb-4">
    <!-- Pie Chart Klasifikasi Studi -->
    <div class="col-12 col-lg-6">
        <div class="stat-breakdown-card">
            <h6 class="fw-bold mb-3">Klasifikasi Studi</h6>
            <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                <canvas id="chartStudi"></canvas>
            </div>
        </div>
    </div>

    <!-- Pie Chart Klasifikasi Pekerjaan -->
    <div class="col-12 col-lg-6">
        <div class="stat-breakdown-card">
            <h6 class="fw-bold mb-3">Klasifikasi Pekerjaan</h6>
            <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                <canvas id="chartPekerjaan"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="table-panel">
    <div class="table-panel-header">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0 fw-bold">Daftar Alumni</h5>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2" 
                        onclick="downloadTemplate()" 
                        type="button" 
                        title="Unduh template Excel">
                    <span class="material-symbols-outlined fs-6">download</span>
                    <span class="d-none d-md-inline">Template</span>
                </button>
                <a href="{{ route('alumni.export_data') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2" title="Ekspor data Alumni">
                    <span class="material-symbols-outlined fs-6">download</span>
                    <span class="d-none d-md-inline">Ekspor</span>
                </a>
                <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalImport" 
                        type="button" 
                        title="Impor data Alumni">
                    <span class="material-symbols-outlined fs-6">upload</span>
                    <span class="d-none d-md-inline">Impor</span>
                </button>
            </div>
        </div>
        <!-- Search Bar - Live Search -->
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="liveSearchInput" 
                           placeholder="Cari alumni..." 
                           style="min-height: 42px;">
                    <button class="btn btn-soft px-3" type="button" style="min-height: 42px;">
                        <span class="material-symbols-outlined fs-5" style="font-size: 1.2rem;">search</span>
                    </button>
                    <button class="btn btn-outline-secondary px-3" id="clearSearchBtn" type="button" style="min-height: 42px; display: none;">
                        <span class="material-symbols-outlined fs-5" style="font-size: 1.2rem;">close</span>
                    </button>
                </div>
            </div>
            <div class="col-12 col-md-auto">
                <small class="text-soft" id="searchCounter" style="display: none;">
                    <span id="resultCount">0</span> data ditemukan
                </small>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Lengkap</th>
                    <th>Tahun Lulus</th>
                    <th>Status</th>
                    <th>Detail</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="alumniTableBody">
                @forelse($alumni as $key => $item)
                    <tr>
                        <td>{{ $alumni->firstItem() + $key }}</td>
                        <td class="fw-600">{{ $item->nama_lengkap }}</td>
                        <td>{{ $item->tahun_lulus }}</td>
                        <td>
                            @if($item->status === 'Melanjutkan Studi')
                                <span class="badge bg-primary">{{ $item->status }}</span>
                            @else
                                <span class="badge bg-success">{{ $item->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($item->status === 'Melanjutkan Studi')
                                <small class="text-soft">{{ $item->jenis_studi }} ({{ $item->jalur_penerimaan }})</small>
                            @else
                                <small class="text-soft">{{ $item->jenis_pekerjaan }}</small>
                            @endif
                            <br>
                            <small class="text-soft">{{ Str::limit($item->keterangan ?? '-', 50) }}</small>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-soft d-inline-flex align-items-center gap-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalAlumni"
                                    onclick="editAlumni({{ json_encode($item) }})" 
                                    type="button" 
                                    title="Edit">
                                <span class="material-symbols-outlined fs-6">edit</span>
                            </button>
                            <form action="{{ route('alumni.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-soft d-inline-flex align-items-center gap-1 text-danger" type="submit" title="Hapus">
                                    <span class="material-symbols-outlined fs-6">delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-soft py-5">
                            <div>
                                <span class="material-symbols-outlined" style="font-size: 3rem; opacity: 0.3;">person_off</span>
                                <p class="mt-3 mb-0">
                                    @if(!empty($search))
                                        Tidak ada hasil pencarian untuk "<strong>{{ $search }}</strong>"
                                    @else
                                        Belum ada data alumni
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
             </tbody>
         </table>
     </div>

     <!-- Pagination Section -->
     <div class="pagination-footer">
         <div class="row align-items-center g-3">
             <div class="col-12 col-md-auto">
                 <div class="d-flex align-items-center gap-2">
                     <label for="perPageSelector" class="form-label mb-0 small fw-600">Tampilkan:</label>
                     <select id="perPageSelector" class="form-select form-select-sm" style="width: auto;">
                         <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                         <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                         <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                         <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                     </select>
                     <span class="small text-soft">data per halaman</span>
                 </div>
             </div>
              <div class="col-12 col-md">
                  @if($alumni->hasPages())
                      <div class="d-flex justify-content-center justify-content-md-end">
                          {{ $alumni->appends(['per_page' => $perPage])->links('pagination::bootstrap-5') }}
                      </div>
                  @endif
              </div>
         </div>
     </div>
 </div>


<!-- Modal Tambah/Edit Alumni -->
<div class="modal fade" id="modalAlumni" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalAlumniTitle">Tambah Alumni</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAlumni" method="POST" action="{{ route('alumni.store') }}">
                @csrf
                <input type="hidden" id="alumniId" name="alumni_id">
                <input type="hidden" id="formMethod" name="_method" value="POST">

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="namaLengkap" class="form-label fw-600">Nama Lengkap Alumni <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="namaLengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="mb-3">
                        <label for="tahunLulus" class="form-label fw-600">Tahun Lulus <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="tahunLulus" name="tahun_lulus" min="1900" max="{{ date('Y') + 10 }}" placeholder="Tahun lulus" required>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label fw-600">Status Setelah Lulus <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required onchange="toggleStatusFields()">
                            <option value="">-- Pilih Status --</option>
                            <option value="Melanjutkan Studi">Melanjutkan Studi</option>
                            <option value="Bekerja">Bekerja</option>
                        </select>
                    </div>

                    <!-- Fields untuk Melanjutkan Studi -->
                    <div id="studyFieldsWrapper" style="display: none;">
                        <div class="mb-3">
                            <label for="jenisStudi" class="form-label fw-600">Jenis Studi <span class="text-danger">*</span></label>
                            <select class="form-select" id="jenisStudi" name="jenis_studi">
                                <option value="">-- Pilih Jenis Studi --</option>
                                <option value="PTN">PTN (Perguruan Tinggi Negeri)</option>
                                <option value="PTS">PTS (Perguruan Tinggi Swasta)</option>
                                <option value="KEDINASAN">KEDINASAN</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="jalurPenerimaan" class="form-label fw-600">Jalur Penerimaan <span class="text-danger">*</span></label>
                            <select class="form-select" id="jalurPenerimaan" name="jalur_penerimaan">
                                <option value="">-- Pilih Jalur Penerimaan --</option>
                                <option value="SNBP">SNBP</option>
                                <option value="SNBT">SNBT</option>
                                <option value="MANDIRI">MANDIRI</option>
                                <option value="KEDINASAN">KEDINASAN</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="keteranganStudi" class="form-label fw-600">Keterangan Jurusan/Prodi - Universitas</label>
                            <textarea class="form-control" id="keteranganStudi" name="keterangan_studi" placeholder="Contoh: Teknik Informatika - Universitas Gadjah Mada" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Fields untuk Bekerja -->
                    <div id="workFieldsWrapper" style="display: none;">
                        <div class="mb-3">
                            <label for="jenisPekerjaan" class="form-label fw-600">Jenis Pekerjaan <span class="text-danger">*</span></label>
                            <select class="form-select" id="jenisPekerjaan" name="jenis_pekerjaan">
                                <option value="">-- Pilih Jenis Pekerjaan --</option>
                                <option value="ASN">ASN (Aparatur Sipil Negara)</option>
                                <option value="TNI">TNI</option>
                                <option value="POLRI">POLRI</option>
                                <option value="SWASTA">SWASTA</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="keteranganKerja" class="form-label fw-600">Keterangan Bidang - Jabatan</label>
                            <textarea class="form-control" id="keteranganKerja" name="keterangan_kerja" placeholder="Contoh: IT Developer - PT Telkom" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Alumni -->
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Impor Data Alumni</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formImport" method="POST" action="{{ route('alumni.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-soft mb-3">Pilih file CSV untuk mengimpor data alumni. <a href="{{ route('alumni.export_template') }}" download>Download template</a> untuk melihat format yang tepat.</p>
                    
                    <div class="mb-3">
                        <label for="fileImport" class="form-label fw-600">Pilih File CSV <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="fileImport" name="file" accept=".csv,.txt" required>
                        <small class="text-soft">Format: CSV | Ukuran maksimal: 2MB</small>
                    </div>

                    <div class="alert alert-info" role="alert">
                        <strong>Catatan Format:</strong>
                        <ul class="mb-0 mt-2 small">
                            <li>Gunakan separator semicolon (;) dalam file CSV</li>
                            <li><strong>Melanjutkan Studi:</strong> Jenis Studi (PTN/PTS/KEDINASAN), Jalur (SNBP/SNBT/MANDIRI/KEDINASAN) wajib diisi</li>
                            <li><strong>Bekerja:</strong> Jenis Pekerjaan (ASN/TNI/POLRI/SWASTA) wajib diisi</li>
                        </ul>
                    </div>
                </div>

                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Impor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .fw-600 {
        font-weight: 600;
    }

    /* Modal Support Dark & Light Mode */
    .modal-content {
        background-color: rgba(255, 255, 255, 0.98);
        border: 1px solid rgba(255, 255, 255, 0.95);
        color: var(--text-main);
    }

    .modal-header {
        border-color: rgba(15, 107, 125, 0.08) !important;
        background: transparent;
    }

    .modal-footer {
        border-color: rgba(15, 107, 125, 0.08) !important;
        background: transparent;
    }

    .modal-body .alert {
        background: rgba(13, 202, 240, 0.1);
        border: 1px solid rgba(13, 202, 240, 0.3);
        color: var(--text-main);
        border-radius: 14px;
    }

    .modal-body a {
        color: var(--brand-700);
        text-decoration: none;
    }

    .modal-body a:hover {
        color: var(--brand-800);
        text-decoration: underline;
    }

    html[data-theme="dark"] .modal-content {
        background-color: rgba(15, 35, 41, 0.98);
        border-color: rgba(180, 221, 227, 0.12) !important;
    }

    html[data-theme="dark"] .modal-header {
        border-color: rgba(180, 221, 227, 0.12) !important;
    }

    html[data-theme="dark"] .modal-footer {
        border-color: rgba(180, 221, 227, 0.12) !important;
    }

    html[data-theme="dark"] .modal-body .alert {
        background: rgba(99, 199, 210, 0.08);
        border-color: rgba(99, 199, 210, 0.25) !important;
    }

    html[data-theme="dark"] .modal-body a {
        color: #9ae7ef;
    }

    html[data-theme="dark"] .modal-body a:hover {
        color: #63c7d2;
    }

    #fileImport.form-control {
        background-color: #fff !important;
        color: var(--text-main) !important;
    }

    html[data-theme="dark"] #fileImport.form-control {
        background-color: #112a31 !important;
        border-color: rgba(180, 221, 227, 0.15) !important;
    }

    /* Search Bar Styling */
    .input-group .form-control {
        border-radius: 14px 0 0 14px;
    }

    .input-group .btn-soft {
        border-radius: 0 14px 14px 0;
        border-left: 1px solid rgba(15, 107, 125, 0.1);
    }

    .input-group .btn-outline-secondary {
        border-radius: 0 14px 14px 0;
        border-left: none;
    }

    html[data-theme="dark"] .input-group .btn-soft {
        border-left-color: rgba(180, 221, 227, 0.15);
    }

    /* ===== Statistik Cards ===== */
    .stat-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 16px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }

    html[data-theme="dark"] .stat-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-icon .material-symbols-outlined {
        font-size: 1.6rem;
        color: #fff;
    }

    .stat-content {
        min-width: 0;
    }

    .stat-label {
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--text-soft);
        margin-bottom: 0.15rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .stat-value {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        line-height: 1.2;
    }

    /* ===== Breakdown Stat Cards ===== */
    .stat-breakdown-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.2s ease;
    }

    .stat-breakdown-card h6 {
        color: var(--text-main);
    }

    /* ===== Table Styling ===== */
    .table-panel {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 16px;
        overflow: hidden;
    }

    .table-responsive {
        border-top: 1px solid var(--line);
    }

    .table {
        background: var(--surface);
        color: var(--text-main);
    }

    .table thead th {
        background: var(--surface-soft);
        border-bottom: 1px solid var(--line);
        color: var(--text-main);
        font-weight: 600;
        padding: 1rem;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .table tbody tr {
        border-bottom: 1px solid var(--line);
        transition: background-color 0.15s ease;
    }

    .table tbody tr:last-child {
        border-bottom: none;
    }

    .table tbody tr:hover {
        background: var(--surface-soft);
    }

    .table td {
        padding: 1rem;
        border: none;
        vertical-align: middle;
    }

    html[data-theme="dark"] .table {
        border-color: rgba(180, 221, 227, 0.12);
    }

    html[data-theme="dark"] .table thead th {
        border-color: rgba(180, 221, 227, 0.12);
        background: rgba(99, 199, 210, 0.06);
    }

    html[data-theme="dark"] .table tbody tr {
        border-color: rgba(180, 221, 227, 0.08);
    }

    html[data-theme="dark"] .table tbody tr:hover {
        background: rgba(99, 199, 210, 0.08);
    }

    /* ===== Table Panel Header ===== */
    .table-panel-header {
        background: var(--surface);
        border-bottom: 1px solid var(--line);
        padding: 1.5rem;
    }

    .table-panel-header h5 {
        color: var(--text-main);
    }

    /* ===== Form Controls ===== */
    .form-control,
    .form-select {
        background-color: var(--surface);
        border: 1px solid var(--line);
        color: var(--text-main);
        transition: all 0.15s ease;
    }

    .form-control:focus,
    .form-select:focus {
        background-color: var(--surface);
        border-color: var(--brand-700);
        color: var(--text-main);
        box-shadow: 0 0 0 0.2rem rgba(15, 107, 125, 0.15);
    }

    html[data-theme="dark"] .form-control,
    html[data-theme="dark"] .form-select {
        background-color: rgba(99, 199, 210, 0.08);
        border-color: rgba(180, 221, 227, 0.15);
    }

    html[data-theme="dark"] .form-control:focus,
    html[data-theme="dark"] .form-select:focus {
        background-color: rgba(99, 199, 210, 0.12);
        border-color: #63c7d2;
        box-shadow: 0 0 0 0.2rem rgba(99, 199, 210, 0.15);
    }

    /* ===== Buttons ===== */
    .btn-outline-secondary {
        color: var(--text-main);
        border-color: var(--line);
    }

    .btn-outline-secondary:hover {
        background-color: var(--surface-soft);
        border-color: var(--text-soft);
    }

    html[data-theme="dark"] .btn-outline-secondary {
        border-color: rgba(180, 221, 227, 0.2);
    }

    html[data-theme="dark"] .btn-outline-secondary:hover {
        background-color: rgba(99, 199, 210, 0.1);
        border-color: #63c7d2;
    }

    /* ===== Badges ===== */
    .badge {
        font-weight: 600;
    }

    /* ===== Input Group ===== */
    .input-group .btn-soft {
        background: var(--surface-soft);
        color: var(--text-main);
        border: 1px solid var(--line);
    }

    .input-group .btn-soft:hover {
        background: var(--surface-muted);
        color: var(--text-main);
    }

    html[data-theme="dark"] .input-group .btn-soft {
        background: rgba(99, 199, 210, 0.08);
        border-color: rgba(180, 221, 227, 0.15);
    }

    html[data-theme="dark"] .input-group .btn-soft:hover {
        background: rgba(99, 199, 210, 0.12);
        border-color: #63c7d2;
    }

    /* ===== Pagination Footer ===== */
    .pagination-footer {
        background: var(--surface);
        border-top: 1px solid var(--line);
        padding: 1.5rem;
        border-radius: 0 0 16px 16px;
    }

    .form-select-sm {
        padding: 0.35rem 0.75rem;
        font-size: 0.875rem;
        height: auto;
    }

    /* Pagination Links Styling */
    .pagination {
        margin: 0;
        gap: 0.25rem;
    }

    .pagination .page-link {
        background: var(--surface-soft);
        border: 1px solid var(--line);
        color: var(--text-main);
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        transition: all 0.15s ease;
    }

    .pagination .page-link:hover {
        background: var(--surface-muted);
        border-color: var(--brand-700);
        color: var(--brand-700);
    }

    .pagination .page-item.active .page-link {
        background: var(--brand-700);
        border-color: var(--brand-700);
        color: white;
        font-weight: 600;
    }

    .pagination .page-item.disabled .page-link {
        background: var(--surface-soft);
        border-color: var(--line);
        color: var(--text-soft);
        opacity: 0.5;
    }

    html[data-theme="dark"] .pagination .page-link {
        background: rgba(99, 199, 210, 0.08);
        border-color: rgba(180, 221, 227, 0.15);
    }

    html[data-theme="dark"] .pagination .page-link:hover {
        background: rgba(99, 199, 210, 0.15);
        border-color: #63c7d2;
        color: #63c7d2;
    }

    html[data-theme="dark"] .pagination .page-item.active .page-link {
        background: #63c7d2;
        border-color: #63c7d2;
        color: #0f2329;
    }

    html[data-theme="dark"] .pagination .page-item.disabled .page-link {
        background: rgba(99, 199, 210, 0.08);
        border-color: rgba(180, 221, 227, 0.1);
        color: var(--text-soft);
    }

</style>

<script>
    function downloadTemplate() {
        window.location.href = '{{ route("alumni.export_template") }}';
    }

    // ===== LIVE SEARCH =====
    let searchTimeout;

    function loadSearchResults(query) {
        const tbody = document.getElementById('alumniTableBody');
        const counter = document.getElementById('searchCounter');
        const countSpan = document.getElementById('resultCount');
        const clearBtn = document.getElementById('clearSearchBtn');

        // Jika query kosong, load semua data
        const url = query.trim() 
            ? '{{ route("alumni.search") }}?q=' + encodeURIComponent(query.trim())
            : '{{ route("alumni.search") }}';

        fetch(url)
            .then(res => res.json())
            .then(data => {
                // Update counter
                if (query.trim()) {
                    counter.style.display = 'inline';
                    countSpan.textContent = data.count;
                } else {
                    counter.style.display = 'none';
                }

                // Tampilkan/sembunyikan clear button
                clearBtn.style.display = query.trim() ? 'flex' : 'none';

                // Render table rows
                if (data.data.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center text-soft py-5">
                                <div>
                                    <span class="material-symbols-outlined" style="font-size: 3rem; opacity: 0.3;">person_off</span>
                                    <p class="mt-3 mb-0">${query.trim() ? 'Tidak ada hasil pencarian untuk "<strong>' + query.trim() + '</strong>"' : 'Belum ada data alumni'}</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }

                let html = '';
                data.data.forEach((item, index) => {
                    const statusBadge = item.status === 'Melanjutkan Studi' 
                        ? '<span class="badge bg-primary">' + item.status + '</span>'
                        : '<span class="badge bg-success">' + item.status + '</span>';

                    let detailHtml = '';
                    if (item.status === 'Melanjutkan Studi') {
                        detailHtml = '<small class="text-soft">' + (item.jenis_studi || '') + ' (' + (item.jalur_penerimaan || '') + ')</small><br><small class="text-soft">' + (item.keterangan ? item.keterangan.substring(0, 50) : '-') + '</small>';
                    } else {
                        detailHtml = '<small class="text-soft">' + (item.jenis_pekerjaan || '') + '</small><br><small class="text-soft">' + (item.keterangan ? item.keterangan.substring(0, 50) : '-') + '</small>';
                    }

                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td class="fw-600">${escapeHtml(item.nama_lengkap)}</td>
                            <td>${item.tahun_lulus}</td>
                            <td>${statusBadge}</td>
                            <td>${detailHtml}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-soft d-inline-flex align-items-center gap-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalAlumni"
                                        onclick="editAlumni(${JSON.stringify(item).replace(/"/g, '&quot;')})" 
                                        type="button" 
                                        title="Edit">
                                    <span class="material-symbols-outlined fs-6">edit</span>
                                </button>
                                <form action="/alumni/${item.id}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button class="btn btn-sm btn-soft d-inline-flex align-items-center gap-1 text-danger" type="submit" title="Hapus">
                                        <span class="material-symbols-outlined fs-6">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                     `;
                    });
                    tbody.innerHTML = html;
                })
                .catch(err => console.error('Search error:', err));
        }


    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    // Live search on input
    document.getElementById('liveSearchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const val = this.value;
        searchTimeout = setTimeout(function() {
            loadSearchResults(val);
        }, 300); // Debounce 300ms
    });

    // Clear search
    document.getElementById('clearSearchBtn').addEventListener('click', function() {
        document.getElementById('liveSearchInput').value = '';
        document.getElementById('clearSearchBtn').style.display = 'none';
        document.getElementById('searchCounter').style.display = 'none';
        loadSearchResults('');
        document.getElementById('liveSearchInput').focus();
    });

    // Trigger initial load jika ada search dari URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const initialSearch = urlParams.get('search');
        if (initialSearch) {
            document.getElementById('liveSearchInput').value = initialSearch;
            loadSearchResults(initialSearch);
        }
    });

    // ===== END LIVE SEARCH =====

    // ===== PAGINATION PER PAGE SELECTOR - AJAX =====
    function loadTableData(perPage = 10, page = 1) {
        fetch('{{ route("alumni.table_data") }}?per_page=' + perPage + '&page=' + page)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('alumniTableBody');
                tbody.innerHTML = data.html;
                
                // Update pagination container
                const paginationContainer = document.querySelector('.pagination-footer .col-12.col-md:last-child');
                if (paginationContainer) {
                    paginationContainer.innerHTML = data.pagination;
                    // Re-attach event listeners untuk pagination links baru
                    attachPaginationListeners();
                }
            })
            .catch(err => console.error('Error loading table data:', err));
    }

    function attachPaginationListeners() {
        // Select semua pagination links (kecuali disabled/span)
        const paginationLinks = document.querySelectorAll('.pagination a.page-link');
        paginationLinks.forEach(link => {
            link.removeEventListener('click', handlePaginationClick); // Remove listener lama
            link.addEventListener('click', handlePaginationClick);
        });
    }

    function handlePaginationClick(e) {
        e.preventDefault();
        const url = new URL(this.href, window.location.origin);
        const page = url.searchParams.get('page') || 1;
        const perPage = document.getElementById('perPageSelector').value;
        loadTableData(perPage, page);
        // Scroll ke top tabel
        document.querySelector('.table-panel').scrollIntoView({ behavior: 'smooth' });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const perPageSelector = document.getElementById('perPageSelector');
        if (perPageSelector) {
            perPageSelector.addEventListener('change', function() {
                const perPage = this.value;
                loadTableData(perPage, 1); // Reset ke halaman 1
                // Scroll ke top tabel
                document.querySelector('.table-panel').scrollIntoView({ behavior: 'smooth' });
            });
        }

         // Attach pagination listeners saat pertama kali load
         attachPaginationListeners();
     });
     // ===== END PAGINATION =====





    function toggleStatusFields() {
        const status = document.getElementById('status').value;
        const studyWrapper = document.getElementById('studyFieldsWrapper');
        const workWrapper = document.getElementById('workFieldsWrapper');
        const jenisStudi = document.getElementById('jenisStudi');
        const jalurPenerimaan = document.getElementById('jalurPenerimaan');
        const jenisPekerjaan = document.getElementById('jenisPekerjaan');
        const keteranganStudi = document.getElementById('keteranganStudi');
        const keteranganKerja = document.getElementById('keteranganKerja');

        if (status === 'Melanjutkan Studi') {
            studyWrapper.style.display = 'block';
            workWrapper.style.display = 'none';
            jenisStudi.required = true;
            jalurPenerimaan.required = true;
            jenisPekerjaan.required = false;
        } else if (status === 'Bekerja') {
            studyWrapper.style.display = 'none';
            workWrapper.style.display = 'block';
            jenisStudi.required = false;
            jalurPenerimaan.required = false;
            jenisPekerjaan.required = true;
        } else {
            studyWrapper.style.display = 'none';
            workWrapper.style.display = 'none';
        }
    }

     function editAlumni(alumni) {
         document.getElementById('modalAlumniTitle').textContent = 'Edit Alumni';
         document.getElementById('alumniId').value = alumni.id;
         document.getElementById('namaLengkap').value = alumni.nama_lengkap;
         document.getElementById('tahunLulus').value = alumni.tahun_lulus;
         document.getElementById('status').value = alumni.status;
         
         toggleStatusFields();
         
         if (alumni.status === 'Melanjutkan Studi') {
             document.getElementById('jenisStudi').value = alumni.jenis_studi || '';
             document.getElementById('jalurPenerimaan').value = alumni.jalur_penerimaan || '';
             document.getElementById('keteranganStudi').value = alumni.keterangan || '';
             document.getElementById('keteranganKerja').value = ''; // Clear kerja field
         } else if (alumni.status === 'Bekerja') {
             document.getElementById('jenisPekerjaan').value = alumni.jenis_pekerjaan || '';
             document.getElementById('keteranganKerja').value = alumni.keterangan || '';
             document.getElementById('keteranganStudi').value = ''; // Clear studi field
         }
         
         document.getElementById('formMethod').value = 'PUT';
         document.getElementById('formAlumni').action = '/alumni/' + alumni.id;
     }


     // Reset form ketika modal ditutup
     document.getElementById('modalAlumni').addEventListener('hidden.bs.modal', function() {
         document.getElementById('modalAlumniTitle').textContent = 'Tambah Alumni';
         document.getElementById('formAlumni').reset();
         document.getElementById('formMethod').value = 'POST';
         document.getElementById('formAlumni').action = '{{ route("alumni.store") }}';
         toggleStatusFields();
     });

     // ===== PIE CHARTS =====
     document.addEventListener('DOMContentLoaded', function() {
         const isDarkMode = document.documentElement.dataset.theme === 'dark';
         const textColor = isDarkMode ? '#e7f3f5' : '#18323a';
         const borderColor = isDarkMode ? 'rgba(180, 221, 227, 0.15)' : 'rgba(15, 107, 125, 0.1)';

         // Chart Klasifikasi Studi
         const ctxStudi = document.getElementById('chartStudi');
         if (ctxStudi) {
             new Chart(ctxStudi, {
                 type: 'doughnut',
                 data: {
                     labels: ['PTN', 'PTS', 'KEDINASAN'],
                     datasets: [{
                         data: [{{ $stats['ptn'] }}, {{ $stats['pts'] }}, {{ $stats['kedinasan_studi'] }}],
                         backgroundColor: ['#0D84E8', '#63C7D2', '#FFB74D'],
                         borderColor: [borderColor, borderColor, borderColor],
                         borderWidth: 2,
                         borderRadius: 8,
                     }]
                 },
                 options: {
                     responsive: true,
                     maintainAspectRatio: true,
                     plugins: {
                         legend: {
                             position: 'bottom',
                             labels: {
                                 color: textColor,
                                 font: { size: 13, weight: '600' },
                                 padding: 16,
                                 usePointStyle: true,
                                 pointStyle: 'circle',
                                 boxWidth: 10,
                             }
                         },
                         tooltip: {
                             backgroundColor: isDarkMode ? 'rgba(15, 35, 41, 0.9)' : 'rgba(24, 50, 58, 0.9)',
                             titleColor: textColor,
                             bodyColor: textColor,
                             borderColor: borderColor,
                             borderWidth: 1,
                             padding: 12,
                             titleFont: { size: 13, weight: 'bold' },
                             bodyFont: { size: 12 },
                         }
                     }
                 }
             });
         }

         // Chart Klasifikasi Pekerjaan
         const ctxPekerjaan = document.getElementById('chartPekerjaan');
         if (ctxPekerjaan) {
             new Chart(ctxPekerjaan, {
                 type: 'doughnut',
                 data: {
                     labels: ['ASN', 'TNI', 'POLRI', 'SWASTA'],
                     datasets: [{
                         data: [{{ $stats['asn'] }}, {{ $stats['tni'] }}, {{ $stats['polri'] }}, {{ $stats['swasta'] }}],
                         backgroundColor: ['#28A745', '#0D84E8', '#FF6B6B', '#FFC107'],
                         borderColor: [borderColor, borderColor, borderColor, borderColor],
                         borderWidth: 2,
                         borderRadius: 8,
                     }]
                 },
                 options: {
                     responsive: true,
                     maintainAspectRatio: true,
                     plugins: {
                         legend: {
                             position: 'bottom',
                             labels: {
                                 color: textColor,
                                 font: { size: 13, weight: '600' },
                                 padding: 16,
                                 usePointStyle: true,
                                 pointStyle: 'circle',
                                 boxWidth: 10,
                             }
                         },
                         tooltip: {
                             backgroundColor: isDarkMode ? 'rgba(15, 35, 41, 0.9)' : 'rgba(24, 50, 58, 0.9)',
                             titleColor: textColor,
                             bodyColor: textColor,
                             borderColor: borderColor,
                             borderWidth: 1,
                             padding: 12,
                             titleFont: { size: 13, weight: 'bold' },
                             bodyFont: { size: 12 },
                         }
                     }
                 }
             });
         }
 
     });

</script>
@endsection
