<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk | SILAWAS</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        body: ['Inter', 'sans-serif'],
                        headline: ['Manrope', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#e9f7f8',
                            100: '#d4eff2',
                            200: '#a7dce2',
                            300: '#77c6d1',
                            400: '#4aaeba',
                            500: '#2c94a1',
                            600: '#1d7784',
                            700: '#155c67',
                            800: '#114b54',
                            900: '#0b333a',
                        },
                    },
                    boxShadow: {
                        soft: '0 24px 60px rgba(11, 51, 58, 0.14)',
                        panel: '0 18px 40px rgba(11, 51, 58, 0.08)',
                    },
                },
            },
        };
    </script>

    <style>
        :root {
            color-scheme: light;
        }

        html, body {
            min-height: 100%;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(44, 148, 161, 0.18), transparent 28%),
                radial-gradient(circle at 80% 20%, rgba(244, 162, 97, 0.16), transparent 22%),
                linear-gradient(180deg, #f5f9fb 0%, #eef4f6 100%);
            color: #16333a;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .login-panel {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.78);
            box-shadow: 0 28px 70px rgba(11, 51, 58, 0.12);
        }

        .brand-tile {
            background:
                radial-gradient(circle at top right, rgba(244, 162, 97, 0.22), transparent 28%),
                linear-gradient(145deg, rgba(11, 51, 58, 0.96), rgba(17, 75, 84, 0.96));
        }

        .brand-mark {
            width: 64px;
            height: 64px;
            border-radius: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2c94a1, #155c67);
            color: #fff;
            box-shadow: 0 18px 30px rgba(44, 148, 161, 0.28);
        }

        .field-shell {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(22, 51, 58, 0.08);
            box-shadow: 0 10px 24px rgba(11, 51, 58, 0.05);
        }

        .field-shell:focus-within {
            border-color: rgba(44, 148, 161, 0.28);
            box-shadow: 0 0 0 4px rgba(44, 148, 161, 0.08);
        }

        .login-input {
            background: transparent;
            border: 0;
            outline: 0;
            box-shadow: none !important;
            color: #16333a;
        }

        .login-input::placeholder {
            color: #8da2a8;
        }

        .login-btn {
            background: linear-gradient(135deg, #155c67, #2c94a1);
            box-shadow: 0 18px 30px rgba(21, 92, 103, 0.22);
        }

        .login-btn:hover {
            filter: brightness(1.04);
        }

        .tiny-label {
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-size: 0.7rem;
            font-weight: 800;
        }

        .frost-line {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.45), transparent);
        }
    </style>
</head>
<body class="font-body">
    <main class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto flex min-h-[calc(100vh-3rem)] w-full max-w-6xl items-center">
            <div class="grid w-full overflow-hidden rounded-[2rem] login-panel lg:grid-cols-[1.05fr_0.95fr]">
                <section class="relative hidden overflow-hidden brand-tile p-10 text-white lg:flex lg:min-h-[760px] lg:flex-col lg:justify-between">
                    <div class="absolute inset-0 opacity-20">
                        <div class="absolute left-10 top-10 h-64 w-64 rounded-full bg-white/20 blur-3xl"></div>
                        <div class="absolute bottom-10 right-10 h-72 w-72 rounded-full bg-brand-300/30 blur-3xl"></div>
                    </div>

                    <div class="relative z-10">
                        <div class="mb-8 flex items-center gap-4">
                            <div class="brand-mark">
                                <span class="material-symbols-outlined text-[30px]">shield_person</span>
                            </div>
                            <div>
                                <div class="font-headline text-3xl font-extrabold tracking-tight">SILAWAS</div>
                                <div class="tiny-label text-white/70">Sistem Layanan Pengawasan</div>
                                <div class="tiny-label text-white/70">dan Pendampingan Sekolah</div>
                            </div>
                        </div>

                        <div class="max-w-lg">
                            <div class="tiny-label mb-4 text-white/65">Login Portal</div>
                            <h1 class="font-headline text-3xl font-extrabold leading-tight">
                                Layanan administrasi komprehensif yang memberikan rekomendasi pengawasan dan pendampingan satuan pendidikan.
                            </h1>
                            <p class="mt-5 text-sm leading-7 text-white/75">
                                Akses dashboard, jurnal kepsek, KBM, dan administrasi sekolah melalui satu pintu yang holistik, komprehensif dan profesional.
                            </p>
                        </div>
                    </div>

                    <div class="relative z-10 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                            <div class="tiny-label text-white/60">Dashboard</div>
                            <div class="mt-2 text-sm font-semibold text-white">Pantau performa sekolah</div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                            <div class="tiny-label text-white/60">Jurnal</div>
                            <div class="mt-2 text-sm font-semibold text-white">Catatan harian yang aktual</div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                            <div class="tiny-label text-white/60">Laporan</div>
                            <div class="mt-2 text-sm font-semibold text-white">Proyeksi aktivitas sekolah</div>
                        </div>
                    </div>
                </section>

                <section class="relative flex items-center justify-center p-6 sm:p-10 lg:p-12">
                    <div class="absolute left-6 top-6 h-24 w-24 rounded-full bg-brand-100/80 blur-2xl"></div>
                    <div class="absolute bottom-6 right-6 h-28 w-28 rounded-full bg-orange-100/70 blur-2xl"></div>

                    <div class="relative z-10 w-full max-w-md">
                        <div class="mb-6 flex items-center justify-between gap-4 lg:hidden">
                            <div class="flex items-center gap-3">
                                <div class="brand-mark h-14 w-14 rounded-2xl">
                                    <span class="material-symbols-outlined">shield_person</span>
                                </div>
                                <div>
                                    <div class="font-headline text-2xl font-extrabold">SILAWAS</div>
                                    <div class="tiny-label text-brand-700">School Supervision System</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="tiny-label text-brand-700">Masuk Akun</div>
                            <h2 class="mt-2 font-headline text-3xl font-extrabold text-slate-900">Selamat datang kembali</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Masukkan email dan kata sandi untuk melanjutkan ke panel SILAWAS.
                            </p>
                        </div>

                        <x-auth-session-status class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" :status="session('status')" />

                        @if($errors->any())
                            <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                {{ $errors->first('email') ?: 'Identitas tidak valid. Silakan coba lagi.' }}
                            </div>
                        @endif

                        <div class="rounded-[1.75rem] bg-white/85 p-6 shadow-panel ring-1 ring-white/80">
                            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                                @csrf

                                <div>
                                    <label class="tiny-label mb-2 block text-slate-700" for="email">Email</label>
                                    <div class="field-shell flex items-center gap-3 rounded-2xl px-4 py-3">
                                        <span class="material-symbols-outlined text-slate-400">mail</span>
                                        <input id="email" name="email" value="{{ old('email') }}" type="email" required autofocus autocomplete="username" placeholder="nama@email.com" class="login-input w-full text-sm" />
                                    </div>
                                </div>

                                <div>
                                    <label class="tiny-label mb-2 block text-slate-700" for="password">Password</label>
                                    <div class="field-shell flex items-center gap-3 rounded-2xl px-4 py-3">
                                        <span class="material-symbols-outlined text-slate-400">lock</span>
                                        <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Masukkan password" class="login-input w-full text-sm" />
                                        <button type="button" id="togglePassword" class="text-slate-400 transition hover:text-brand-700" tabindex="-1">
                                            <span class="material-symbols-outlined" id="toggleIcon">visibility_off</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between gap-3">
                                    <label for="remember_me" class="flex items-center gap-2 text-sm text-slate-600">
                                        <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-brand-700 focus:ring-brand-700">
                                        <span>Ingat saya</span>
                                    </label>

                                    @if (Route::has('password.request'))
                                        <a class="text-sm font-semibold text-brand-700 hover:text-brand-900" href="{{ route('password.request') }}">
                                            Lupa password?
                                        </a>
                                    @endif
                                </div>

                                <button type="submit" class="login-btn inline-flex w-full items-center justify-center rounded-2xl px-5 py-3.5 text-sm font-bold text-white transition">
                                    Masuk ke Sistem
                                </button>
                            </form>
                        </div>

                        <div class="mt-6 flex items-center justify-between text-[11px] uppercase tracking-[0.18em] text-slate-400">
                            <span>Secure access</span>
                            <span>v2.6.0</span>
                            <span>Daily supervision</span>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (!togglePassword || !passwordInput || !toggleIcon) {
                return;
            }

            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                toggleIcon.textContent = type === 'password' ? 'visibility_off' : 'visibility';
                this.setAttribute('title', type === 'password' ? 'Tampilkan password' : 'Sembunyikan password');
            });
        });
    </script>
</body>
</html>
