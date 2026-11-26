<div class="max-w-md mx-auto my-10 font-space">
    <div class="bg-white p-8 sm:p-10 rounded-[36px] border border-slate-200/80 shadow-xs">
        <div class="text-center mb-8">
            <!-- Iconic Brand Logo -->
            <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-black flex items-center justify-center text-[#B5F938] shadow-sm">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="7" y1="15" x2="7" y2="9"></line>
                    <line x1="12" y1="18" x2="12" y2="6"></line>
                    <line x1="17" y1="14" x2="17" y2="10"></line>
                </svg>
            </div>
            <h1 class="font-syne text-2xl sm:text-3xl font-extrabold text-black tracking-tight">Daftar Akun Baru</h1>
            <p class="text-xs text-slate-500 mt-1.5">Bergabung untuk menyuarakan aspirasi dan masukan membangun</p>
        </div>

        <form wire:submit="register" class="space-y-4">
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-black mb-1.5">Nama Lengkap</label>
                <input wire:model="name" type="text" id="name" required autofocus
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-black focus:border-black transition-colors"
                    placeholder="Contoh: Budi Santoso">
                @error('name') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-black mb-1.5">Email Kampus / Pribadi</label>
                <input wire:model="email" type="email" id="email" required
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-black focus:border-black transition-colors"
                    placeholder="user@gmail.com">
                @error('email') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-black mb-1.5">Kata Sandi (Min. 8 Karakter)</label>
                <input wire:model="password" type="password" id="password" required
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-black focus:border-black transition-colors"
                    placeholder="••••••••">
                @error('password') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-black mb-1.5">Konfirmasi Kata Sandi</label>
                <input wire:model="password_confirmation" type="password" id="password_confirmation" required
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-black focus:border-black transition-colors"
                    placeholder="••••••••">
            </div>

            <button type="submit" wire:loading.attr="disabled"
                class="w-full py-3.5 px-6 bg-black hover:bg-slate-900 text-white font-bold rounded-full text-xs sm:text-sm shadow-sm transition-all flex items-center justify-center gap-2 disabled:opacity-50 mt-2">
                <span wire:loading.remove>Daftar Sekarang</span>
                <span wire:loading class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-[#B5F938]" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Membuat Akun...
                </span>
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" wire:navigate class="font-bold text-black hover:underline">Masuk di sini</a>
        </div>
    </div>
</div>
