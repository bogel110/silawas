<x-guest-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0;
            background-color: #020617;
        }

        /* Latar belakang tetap dengan efek glow */
        .login-bg {
            background: radial-gradient(circle at top right, rgba(37, 99, 235, 0.15), transparent),
                        radial-gradient(circle at bottom left, rgba(29, 78, 216, 0.15), transparent),
                        linear-gradient(rgba(2, 6, 23, 0.85), rgba(2, 6, 23, 0.85)), 
                        url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=2070');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* Menghilangkan kotak (card), hanya menyisakan wrapper konten */
        .login-box {
            width: 100%;
            max-width: 400px;
            background: transparent; /* Kotak dihilangkan */
            padding: 20px;
            box-shadow: none; /* Shadow dihilangkan */
            border: none; /* Border dihilangkan */
        }

        .input-label {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 12px;
            display: block;
        }

        /* Input tetap menggunakan gaya glassmorphism tipis agar teks terbaca */
        .input-group {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            background: rgba(255, 255, 255, 0.06);
            border-color: #3b82f6;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.1);
        }

        .input-group input {
            color: #ffffff;
            font-size: 0.95rem;
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.15);
        }

        .btn-login {
            background: #2563eb;
            color: white;
            font-weight: 700;
            padding: 16px;
            border-radius: 16px;
            width: 100%;
            border: none;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #3b82f6;
            box-shadow: 0 0 30px rgba(37, 99, 235, 0.3);
        }

        .checkbox-label {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.8rem;
        }

        .footer-text {
            margin-top: 40px;
            padding-top: 24px;
            color: rgba(255, 255, 255, 0.2);
            font-size: 0.6rem;
            letter-spacing: 2px;
            text-align: center;
        }

        .logo-container {
            width: 50px;
            height: 50px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
    </style>

    <div class="login-bg">
        <div class="login-box">
            <div class="text-center mb-12">
                <div class="logo-container">
                    <span class="material-symbols-outlined text-blue-500 text-3xl">SILAWAS</span>
                </div>
                <h3 class="text-3xl font-extrabold text-white tracking-tighter">Sistem Laporan <span class="text-blue-500">& Pengawasan Sekolah</span></h1>
                <p class="text-blue-400/50 text-[9px] mt-2 uppercase tracking-[0.4em] font-bold">Secure Access Portal</p>
            </div>

            <x-auth-session-status class="mb-6 text-sm text-center text-blue-400" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-6">
                    <label class="input-label">Username</label>
                    <div >
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus 
                            class="w-full border-none bg-transparent focus:ring-0 text-black py-4" placeholder="email">
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2 px-1">
                        <label class="input-label mb-0">Password</label>
                    </div>
                    <div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full border-none bg-transparent focus:ring-0 text-black py-4" placeholder="password">
                    </div>
                </div>

                <div class="flex items-center mb-8 px-1">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-white/10 bg-white/5 text-blue-600 focus:ring-0 focus:ring-offset-0">
                    <span class="ms-3 checkbox-label">Remember Session</span>
                    <div class="flex justify-between items-center mb-2 px-1">
                        {{-- <label class="input-label mb-0">Reset</label>
                        @if (Route::has('password.request'))
                            <a class="text-[9px] font-bold text-white/50 hover:text-blue-400 uppercase tracking-widest" href="{{ route('password.request') }}">Reset?</a>
                        @endif --}}
                    </div>
                </div>
                <div class="flex items-center mb-8 px-1">
                    <div class="input-label mb-0 flex justify-between items-center mb-2 px-1">
                        @if (Route::has('password.request'))
                            <a class="rounded border-white/10 bg-white/5 text-blue-600 focus:ring-0 focus:ring-offset-0" href="{{ route('password.request') }}">Reset Password</a>
                        @endif
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    Login
                </button>
            </form>

            <div class="footer-text">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <div class="w-1 h-1 bg-blue-500/50 rounded-full"></div>
                    <span class="text-[8px] uppercase font-bold">Encrypted Connection</span>
                </div>
                &copy; 2026 SILAWAS
            </div>
        </div>
    </div>
</x-guest-layout>