<x-guest-layout>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              "colors": {
                      "surface-dim": "#10141a",
                      "primary-fixed": "#9cf0ff",
                      "primary": "#c3f5ff",
                      "surface-variant": "#31353c",
                      "on-surface": "#dfe2eb",
                      "on-surface-variant": "#bac9cc",
                      "primary-fixed-dim": "#00daf3",
                      "surface": "#10141a",
                      "primary-container": "#00e5ff",
                      "surface-container-highest": "#31353c",
                      "on-primary-fixed": "#001f24",
                      "on-primary": "#00363d",
                      "outline": "#849396",
                      "background": "#10141a",
                      "outline-variant": "#3b494c",
              },
              "fontFamily": {
                      "headline": ["Space Grotesk"],
                      "body": ["Inter"],
                      "label": ["Inter"]
              }
            },
          },
        }
    </script>
    
    <style>
        body {
            min-height: max(884px, 100dvh);
            background-color: #10141a;
            color: #dfe2eb;
            font-family: 'Inter', sans-serif;
            margin: 0;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .glass-panel {
            background: rgba(49, 53, 60, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        .cyan-glow {
            box-shadow: 0 0 15px rgba(0, 229, 255, 0.15);
        }
        .active-pulse {
            position: absolute;
            top: 0;
            left: 0;
            height: 2px;
            width: 30%;
            background: #00e5ff;
            box-shadow: 0 0 10px #00e5ff;
        }
        
        /* Tambahan untuk menghilangkan background autofill browser yang merusak tema gelap */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 30px transparent inset !important;
            -webkit-text-fill-color: #dfe2eb !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>

    <header class="fixed top-0 w-full z-50">
        {{-- <div class="flex items-center text-center h-16 px-6 w-full bg-transparent">
            <div class="flex items-center gap-2">
                <h1 class="text-2xl items-center font-bold tracking-tighter text-cyan-400 font-headline uppercase tracking-widest">SILAWAS</h1>
            </div>
        </div> --}}
    </header>

    <main class="flex-grow flex items-center justify-center px-6 relative h-screen">
        
        <div class="absolute inset-0 z-0 pointer-events-none">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-primary-container/5 rounded-full blur-[120px]"></div>
            <img alt="Deep space nebula with digital connections" class="w-full h-full object-cover opacity-10 grayscale mix-blend-screen" src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=2070"/>
        </div>

        <div class="z-10 w-full max-w-md">
            
            <x-auth-session-status class="mb-4 text-center text-[10px] font-label font-bold text-error uppercase tracking-[0.2em]" :status="session('status')" />
            
            @if($errors->any())
                <div class="mb-4 text-center text-[10px] font-label font-bold text-red-400 uppercase tracking-[0.2em]">
                    Identitas tidak valid. Silakan coba lagi.
                </div>
            @endif

            <div class="glass-panel rounded-xl overflow-hidden relative cyan-glow border border-white/5">
                
                <div class="active-pulse"></div>
                
                <div class="p-8 sm:p-10 flex flex-col gap-8">
                    <div class="space-y-2">
                        <h2 class="text-3xl font-headline font-bold text-primary tracking-tight">SILAWAS SYSTEM ACCESS</h2>
                        <p class="text-on-surface-variant text-[10px] sm:text-sm font-label uppercase tracking-widest">Sistem Laporan dan Pengawasan Sekolah</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <div class="space-y-1">
                            <label class="text-[10px] font-label font-bold text-primary-fixed-dim uppercase tracking-[0.2em] px-1" for="email">Email (Identifier) </label>
                            <div class="relative group">
                                <input id="email" name="email" value="{{ old('email') }}" type="email" required autofocus
                                    class="w-full bg-transparent border-t-0 border-x-0 border-b border-outline-variant py-3 px-1 text-on-surface placeholder:text-outline focus:ring-0 focus:border-primary transition-all duration-300 outline-none font-mono text-sm" 
                                    placeholder="username" autocomplete="username"/>
                                <div class="absolute bottom-0 left-0 h-[1px] w-0 bg-primary group-focus-within:w-full transition-all duration-500 shadow-[0_0_8px_#c3f5ff]"></div>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-label font-bold text-primary-fixed-dim uppercase tracking-[0.2em] px-1" for="password">Security Key</label>
                            <div class="relative group flex items-center border-b border-outline-variant focus-within:border-transparent transition-colors">
                                <input id="password" name="password" type="password" required autocomplete="current-password"
                                    class="w-full bg-transparent border-none py-3 px-1 text-on-surface placeholder:text-outline focus:ring-0 outline-none font-mono text-sm" 
                                    placeholder="password"/>
                                
                                <button type="button" id="togglePassword" class="text-outline hover:text-primary transition-colors px-2 focus:outline-none" tabindex="-1">
                                    <span class="material-symbols-outlined text-[20px]" id="toggleIcon">visibility_off</span>
                                </button>
                                
                                <div class="absolute bottom-0 left-0 h-[1px] w-0 bg-primary group-focus-within:w-full transition-all duration-500 shadow-[0_0_8px_#c3f5ff]"></div>
                            </div>
                        </div>

                        <div class="pt-4 flex flex-col gap-4">
                            <button type="submit" class="w-full py-4 rounded-lg bg-gradient-to-br from-primary to-primary-container text-on-primary-fixed font-label font-bold text-sm uppercase tracking-[0.15em] hover:brightness-110 transition-all active:scale-[0.98] shadow-lg shadow-primary-container/10">
                                Authorize
                            </button>
                            
                            <div class="flex justify-between items-center pt-2">
                                <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                                    <input id="remember_me" type="checkbox" name="remember" class="w-3 h-3 rounded-sm border-outline  text-primary focus:ring-primary focus:ring-offset-0 opacity-50 group-hover:opacity-100 transition-opacity">
                                    <span class="ms-2 text-[10px] font-label  group-hover:text-cyan-400   uppercase">Remember me</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a class="text-[10px] font-label  hover:text-cyan-400 uppercase" href="{{ route('password.request') }}">Reset Password?</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-8 flex justify-center items-center gap-8 sm:gap-12 opacity-40">
                <div class="flex flex-col items-center">
                    <span class="text-[9px] font-label text-primary uppercase tracking-tighter">Status</span>
                    <span class="text-[10px] font-headline font-bold text-on-surface">NOMINAL</span>
                </div>
                <div class="h-8 w-[1px] bg-outline-variant/30"></div>
                <div class="flex flex-col items-center">
                    <span class="text-[9px] font-label text-primary uppercase tracking-tighter">Uptime</span>
                    <span class="text-[10px] font-headline font-bold text-on-surface">99.98%</span>
                </div>
                <div class="h-8 w-[1px] bg-outline-variant/30"></div>
                <div class="flex flex-col items-center">
                    <span class="text-[9px] font-label text-primary uppercase tracking-tighter">Protocol</span>
                    <span class="text-[10px] font-headline font-bold text-on-surface">v2.6.0-S</span>
                </div>
            </div>
        </div>
    </main>

    <footer class="fixed bottom-0 w-full z-50">
        <div class="flex flex-col md:flex-row justify-between items-center px-6 md:px-12 py-6 w-full bg-transparent opacity-80">
            <div class="text-slate-500 font-['Inter'] text-[9px] md:text-[10px] tracking-[0.1em] uppercase mb-4 md:mb-0 text-center">
                © 2026 SILAWAS SYSTEM. ALL RIGHTS RESERVED.
            </div>
            <div class="flex gap-6 md:gap-8">
                <a class="text-slate-600 hover:text-cyan-400 transition-all font-['Inter'] text-[9px] md:text-[10px] tracking-[0.1em] uppercase" href="#">SECURITY</a>
                <a class="text-slate-600 hover:text-cyan-400 transition-all font-['Inter'] text-[9px] md:text-[10px] tracking-[0.1em] uppercase" href="#">SYSTEM STATUS</a>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Ubah teks menjadi ikon mata dari Material Symbols
                toggleIcon.textContent = type === 'password' ? 'visibility_off' : 'visibility';
                this.setAttribute('title', type === 'password' ? 'Tampilkan Security Key' : 'Sembunyikan Security Key');
            });
        });
    </script>
</x-guest-layout>