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
            box-sizing: border-box; 
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
            animation: scanline 4s infinite linear;
        }
        
        @keyframes scanline {
            0% { left: -30%; }
            100% { left: 100%; }
        }
        
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
        <div class="flex items-center justify-center h-16 px-6 w-full bg-transparent">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-cyan-400" data-icon="terminal">terminal</span>
                <h1 class="text-2xl font-bold tracking-tighter text-cyan-400 font-headline uppercase tracking-widest">SILAWAS</h1>
            </div>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center px-6 relative h-screen">
        
        <div class="absolute inset-0 z-0 pointer-events-none">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-primary-container/5 rounded-full blur-[120px]"></div>
            <img alt="Deep space nebula with digital connections" class="w-full h-full object-cover opacity-10 grayscale mix-blend-screen" src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=2070"/>
        </div>

        <div class="z-10 w-full max-w-md">
            
            <x-auth-session-status class="mb-4 text-center text-[10px] font-label font-bold text-primary uppercase tracking-[0.2em]" :status="session('status')" />
            
            @if($errors->any())
                <div class="mb-4 text-center text-[10px] font-label font-bold text-red-400 uppercase tracking-[0.2em]">
                    {{ $errors->first('email') }}
                </div>
            @endif

            <div class="glass-panel rounded-xl overflow-hidden relative cyan-glow border border-white/5">
                
                <div class="active-pulse"></div>
                
                <div class="p-8 sm:p-10 flex flex-col gap-8">
                    
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-2 text-primary">
                            <span class="material-symbols-outlined">lock_reset</span>
                            <h2 class="text-2xl sm:text-3xl font-headline font-bold tracking-tight">System Recovery</h2>
                        </div>
                        <p class="text-on-surface-variant text-[11px] sm:text-[12px] font-label leading-relaxed text-justify opacity-80">
                            Provide your secure identifier (Email) below. The system will transmit a localized protocol link to your terminal, allowing you to establish a new security key.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                        @csrf

                        <div class="space-y-1">
                            <label class="text-[10px] font-label font-bold text-primary-fixed-dim uppercase tracking-[0.2em] px-1" for="email">Identifier (Email)</label>
                            <div class="relative group">
                                <input id="email" name="email" value="{{ old('email') }}" type="email" required autofocus
                                    class="w-full bg-transparent border-t-0 border-x-0 border-b border-outline-variant py-3 px-1 text-on-surface placeholder:text-outline focus:ring-0 focus:border-primary transition-all duration-300 outline-none font-mono text-sm" 
                                    placeholder="USER_ID_HEX" autocomplete="username"/>
                                <div class="absolute bottom-0 left-0 h-[1px] w-0 bg-primary group-focus-within:w-full transition-all duration-500 shadow-[0_0_8px_#c3f5ff]"></div>
                            </div>
                        </div>

                        <div class="pt-2 flex flex-col gap-4">
                            <button type="submit" class="w-full py-4 rounded-lg bg-gradient-to-br from-primary to-primary-container text-on-primary-fixed font-label font-bold text-sm uppercase tracking-[0.15em] hover:brightness-110 transition-all active:scale-[0.98] shadow-lg shadow-primary-container/10">
                                Transmit Reset Link
                            </button>
                            
                            <div class="flex justify-center pt-3 border-t border-outline-variant/30 mt-2">
                                <a href="{{ route('login') }}" class="flex items-center gap-1 text-[10px] font-label text-slate-500 hover:text-cyan-400 transition-colors tracking-widest uppercase group">
                                    <span class="material-symbols-outlined text-[14px] group-hover:-translate-x-1 transition-transform">arrow_left_alt</span>
                                    Abort Recovery / Return
                                </a>
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
                    <span class="text-[9px] font-label text-primary uppercase tracking-tighter">Protocol</span>
                    <span class="text-[10px] font-headline font-bold text-on-surface">RECOVERY</span>
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
</x-guest-layout>