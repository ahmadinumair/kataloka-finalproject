<div class="max-w-3xl mx-auto my-6 font-space">
    <div class="bg-white rounded-[36px] border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Header Banner (Inspired by Lime Box with Dark Text) -->
        <div class="bg-[#B5F938] p-8 sm:p-10 text-black border-b border-black/10">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-black text-white uppercase tracking-wider mb-3">
                ⚡ Analisis AI Aktif
            </span>
            <h1 class="font-syne text-3xl sm:text-4xl font-extrabold tracking-tight">Formulir Aspirasi & Masukan</h1>
            <p class="text-slate-800 text-xs sm:text-sm mt-1.5 font-medium">Sampaikan keluhan atau gagasan Anda. Setiap suara didengar dan diproses secara terbuka.</p>
        </div>

        <form wire:submit="save" class="p-6 sm:p-10 space-y-6">
            <!-- Tipe & Kategori -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="type_id" class="block text-xs font-bold uppercase tracking-wider text-black mb-2">
                        Jenis Masukan <span class="text-rose-500">*</span>
                    </label>
                    <select wire:model="type_id" id="type_id" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-black focus:border-black transition-colors">
                        @foreach($feedbackTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('type_id') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-xs font-bold uppercase tracking-wider text-black mb-2">
                        Kategori Bidang <span class="text-rose-500">*</span>
                    </label>
                    <select wire:model="category_id" id="category_id" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-black focus:border-black transition-colors">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Judul -->
            <div>
                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-black mb-2">
                    Judul Aspirasi / Keluhan <span class="text-rose-500">*</span>
                </label>
                <input wire:model.live.debounce.300ms="title" type="text" id="title" required
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-black focus:border-black transition-colors"
                    placeholder="Contoh: AC Gedung B Lantai 3 Ruang 302 Mati Total">
                <div class="flex items-center justify-between mt-1 text-[11px] text-slate-400">
                    <span>Sebutkan fasilitas dan lokasi secara spesifik.</span>
                    <span>{{ strlen($title) }}/150</span>
                </div>
                @error('title') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Deskripsi Lengkap & Tombol Cek Rangkuman AI -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-black">
                        Deskripsi Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <button type="button" wire:click="generateAiSummary" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-black bg-[#B5F938] hover:bg-[#a1f316] px-4 py-1.5 rounded-full transition-all border border-black/10 shadow-2xs">
                        <span wire:loading.remove wire:target="generateAiSummary">⚡ Cek Rangkuman AI</span>
                        <span wire:loading wire:target="generateAiSummary" class="inline-flex items-center gap-1">
                            <svg class="animate-spin h-3.5 w-3.5 text-black" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            Menganalisis...
                        </span>
                    </button>
                </div>
                <textarea wire:model.live.debounce.300ms="description" id="description" rows="5" required
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-black focus:border-black transition-colors"
                    placeholder="Jelaskan secara jelas apa yang terjadi, sejak kapan, dan dampak yang dialami..."></textarea>
                <div class="flex items-center justify-between mt-1 text-[11px] text-slate-400">
                    <span>Minimal 20 karakter agar dapat dianalisis AI dengan akurat.</span>
                    <span>{{ strlen($description) }}/3000</span>
                </div>
                @error('description') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Upload Foto Bukti / Lampiran (Opsional) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-black mb-1.5">
                    Foto Bukti Kerusakan / Lampiran <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                </label>
                <div class="p-4 bg-slate-50 border border-dashed border-slate-300 rounded-2xl">
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <label class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-slate-100 text-black border border-slate-200 rounded-full text-xs font-bold shadow-2xs transition-all">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Pilih Foto Bukti</span>
                            <input wire:model="image" type="file" accept="image/*" class="hidden">
                        </label>
                        <span class="text-[11px] text-slate-400">Format: JPG, PNG, WEBP, GIF (Maks. 5MB)</span>
                        <div wire:loading wire:target="image" class="inline-flex items-center gap-1.5 text-xs text-slate-600">
                            <svg class="animate-spin h-3.5 w-3.5 text-black" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Mengunggah pratinjau...</span>
                        </div>
                    </div>

                    <!-- Image Preview -->
                    @if($image)
                        <div class="mt-3.5 pt-3.5 border-t border-slate-200/60 flex items-center gap-4">
                            <div class="relative w-20 h-20 rounded-xl overflow-hidden border border-black/10 bg-slate-200 shrink-0 shadow-2xs">
                                <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-black truncate">{{ $image->getClientOriginalName() }}</p>
                                <p class="text-[11px] text-slate-500">{{ round($image->getSize() / 1024, 1) }} KB</p>
                                <button type="button" wire:click="$set('image', null)" class="text-xs text-rose-600 hover:text-rose-700 font-bold mt-1 inline-flex items-center gap-1">
                                    <span>Hapus foto</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
                @error('image') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Hasil Analisis AI Box -->
            @if($ai_summary)
                <div class="p-5 bg-black text-white rounded-[24px] text-xs font-space border border-black/20 shadow-sm transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#B5F938] uppercase tracking-wider">
                            🤖 Hasil Analisis AI Kataloka
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold
                            {{ $urgency === 'urgent' ? 'bg-rose-500 text-white' :
                               ($urgency === 'high' ? 'bg-amber-400 text-black' :
                               ($urgency === 'low' ? 'bg-slate-700 text-slate-200' : 'bg-[#B5F938] text-black')) }}">
                            Urgensi: {{ ucfirst($urgency) }}
                        </span>
                    </div>
                    <p class="text-slate-200 text-xs sm:text-sm italic leading-relaxed">"{{ $ai_summary }}"</p>
                    <p class="text-[10px] text-slate-400 mt-2">Ringkasan padat ini akan langsung tersimpan untuk memudahkan prioritas penanganan pihak kampus.</p>
                </div>
            @endif

            <!-- Opsi Anonim (Privacy Toggle) -->
            <div class="p-4 bg-[#F7F8F5] border border-slate-200 rounded-2xl">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input wire:model="is_anonymous" type="checkbox"
                        class="mt-0.5 rounded border-slate-300 text-black focus:ring-black">
                    <div>
                        <span class="text-xs sm:text-sm font-bold text-black">Kirim Secara Anonim (Sembunyikan Identitas)</span>
                        <p class="text-xs text-slate-500 mt-0.5">Nama Anda tidak akan ditampilkan di papan aspirasi publik demi perlindungan privasi pelapor.</p>
                    </div>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('feedbacks.index') }}" wire:navigate
                    class="px-5 py-3 text-xs font-bold text-slate-600 hover:text-black rounded-full transition-colors">
                    Batal
                </a>
                <button type="submit" wire:loading.attr="disabled"
                    class="px-8 py-3.5 bg-black hover:bg-slate-900 text-white font-bold rounded-full text-xs sm:text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove>🚀 Publikasikan Aspirasi</span>
                    <span wire:loading class="inline-flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-[#B5F938]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
