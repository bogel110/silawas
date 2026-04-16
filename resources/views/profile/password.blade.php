@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-4">
                <h3 class="display-6 fw-bold font-headline text-dark mb-0">Update Password</h3>
            </div>

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-6">
                    <form method="POST" action="{{ route('profile.password.update') }}">
                        @csrf
                        @method('put')

                        {{-- FIELD 1: PASSWORD LAMA --}}
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase tracking-wider text-muted">Password Lama</label>
                            <div class="position-relative">
                                <input type="password" id="current_password" name="current_password" class="form-control form-control-lg bg-light border-2 rounded-3 pe-5 @error('current_password') is-invalid @enderror" required>
                                <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y text-muted toggle-password" data-target="current_password">
                                    <span class="material-symbols-outlined fs-5" id="icon_current_password">visibility_off</span>
                                </button>
                            </div>
                            @error('current_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- FIELD 2: PASSWORD BARU --}}
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase tracking-wider text-muted">Password Baru</label>
                            <div class="position-relative">
                                <input type="password" id="password" name="password" class="form-control form-control-lg bg-light border-2 rounded-3 pe-5 @error('password') is-invalid @enderror" required>
                                <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y text-muted toggle-password" data-target="password">
                                    <span class="material-symbols-outlined fs-5" id="icon_password">visibility_off</span>
                                </button>
                            </div>
                            @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- FIELD 3: KONFIRMASI PASSWORD BARU --}}
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase tracking-wider text-muted">Konfirmasi Password Baru</label>
                            <div class="position-relative">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control-lg bg-light border-2 rounded-3 pe-5" required>
                                <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y text-muted toggle-password" data-target="password_confirmation">
                                    <span class="material-symbols-outlined fs-5" id="icon_password_confirmation">visibility_off</span>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold text-uppercase tracking-widest shadow-sm py-3 rounded-3">
                                Update password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT UNTUK TOGGLE MATA PASSWORD --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.toggle-password');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Ambil ID dari input yang menjadi target
                const targetId = this.getAttribute('data-target');
                const inputField = document.getElementById(targetId);
                const icon = document.getElementById('icon_' + targetId);
                
                // Ubah Tipe Input & Icon
                if (inputField.type === 'password') {
                    inputField.type = 'text';
                    icon.textContent = 'visibility'; // Icon mata terbuka
                    icon.classList.add('text-primary'); // Warnai biru saat terbuka
                } else {
                    inputField.type = 'password';
                    icon.textContent = 'visibility_off'; // Icon mata dicoret
                    icon.classList.remove('text-primary');
                }
            });
        });
    });
</script>
@endsection