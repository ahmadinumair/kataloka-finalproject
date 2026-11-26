<div class="max-w-3xl mx-auto my-6 space-y-6 font-space">
    <!-- Back Navigation -->
    <div>
        <a href="{{ route('feedbacks.index') }}" wire:navigate class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-black transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Beranda
        </a>
    </div>

    <!-- Survey Card -->
    <div class="bg-white rounded-[36px] border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="p-6 sm:p-10 border-b border-slate-100 bg-[#F7F8F5]">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-[#B5F938] text-black uppercase">
                    📋 Kuesioner Evaluasi Kampus
                </span>
                @if($survey->is_active)
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-black text-[#B5F938]">
                        ● Survei Aktif
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-600">
                        ✕ Survei Ditutup
                    </span>
                @endif
            </div>

            <h1 class="font-syne text-2xl sm:text-3xl font-extrabold text-black tracking-tight leading-snug">
                {{ $survey->title }}
            </h1>

            @if($survey->description)
                <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                    {{ $survey->description }}
                </p>
            @endif

            <div class="mt-4 flex flex-wrap items-center gap-3 text-xs text-slate-500 font-medium">
                @if($survey->target_audience)
                    <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-800 text-[11px] font-bold">
                        Sasaran: {{ $survey->target_audience }}
                    </span>
                @endif
                <span>•</span>
                <span>{{ $totalResponses }} mahasiswa telah berpartisipasi</span>
                @if($isAdmin && $averageScore > 0)
                    <span>•</span>
                    <span class="font-bold text-black bg-[#B5F938] px-2.5 py-0.5 rounded-full text-[11px]">
                        Skor Rata-rata (Khusus Admin): {{ $averageScore }}/5.0 ⭐
                    </span>
                @endif
            </div>
        </div>

        <!-- Body -->
        <div class="p-6 sm:p-10">
            @if($hasSubmitted)
                <!-- State: Sudah Mengisi Kuesioner -->
                <div class="text-center py-10 space-y-4">
                    <div class="w-16 h-16 bg-[#B5F938] text-black font-extrabold text-2xl rounded-full flex items-center justify-center mx-auto shadow-sm">
                        ✓
                    </div>
                    <h3 class="font-syne text-xl sm:text-2xl font-bold text-black">
                        Terima Kasih Atas Partisipasi Anda!
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 max-w-md mx-auto leading-relaxed">
                        Tanggapan kuesioner Anda telah berhasil tersimpan dan akan digunakan oleh pimpinan kampus serta dosen pengajar untuk terus meningkatkan mutu perkuliahan dan fasilitas.
                    </p>
                    <div class="pt-4">
                        <a href="{{ route('feedbacks.index') }}" wire:navigate
                            class="inline-flex items-center gap-2 px-6 py-3 bg-black hover:bg-slate-900 text-white rounded-full text-xs font-bold shadow-sm transition-all">
                            <span>Kembali ke Beranda Aspirasi</span>
                            <svg class="w-4 h-4 text-[#B5F938]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            @elseif(!$survey->is_active)
                <!-- State: Survei Sudah Ditutup -->
                <div class="text-center py-10 space-y-3">
                    <div class="w-16 h-16 bg-slate-100 text-slate-400 font-extrabold text-2xl rounded-full flex items-center justify-center mx-auto">
                        ✕
                    </div>
                    <h3 class="font-syne text-lg font-bold text-black">Survei Ini Telah Ditutup</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">Masa pengisian form kuesioner ini telah berakhir. Terima kasih atas perhatian Anda.</p>
                </div>
            @else
                <!-- Form Pengisian Survei -->
                <form wire:submit="submitSurvey" class="space-y-8">
                    @foreach($survey->questions as $index => $q)
                        <div class="p-6 rounded-[24px] bg-[#F7F8F5] border border-slate-200/80 space-y-4">
                            <div class="flex items-start gap-3">
                                <span class="w-7 h-7 rounded-full bg-black text-[#B5F938] font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <h4 class="font-syne text-sm sm:text-base font-bold text-black leading-snug">
                                        {{ $q->question }}
                                    </h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        @if($q->type === 'scale_1_5')
                                            Pilih nilai 1 (Sangat Buruk) hingga 5 (Sangat Baik / Sempurna)
                                        @else
                                            Pilih salah satu tingkat kepuasan di bawah ini
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Pilihan Jawaban Menurun (Vertical Stack) -->
                            @php
                                $options = $q->getAvailableOptions();
                            @endphp

                            <div class="space-y-2 pt-1">
                                @foreach($options as $opt)
                                    <label class="cursor-pointer block">
                                        <input type="radio" wire:model="answers.{{ $q->id }}" value="{{ $opt }}" class="sr-only peer">
                                        <div class="flex items-center justify-between py-3 px-4 rounded-2xl border text-xs font-bold transition-all
                                            peer-checked:bg-black peer-checked:text-[#B5F938] peer-checked:border-black peer-checked:shadow-sm
                                            bg-white text-slate-800 border-slate-200/90 hover:border-black">
                                            <div class="flex items-center gap-3">
                                                <span class="w-5 h-5 rounded-full border-2 border-current flex items-center justify-center shrink-0">
                                                    <span class="w-2 h-2 rounded-full bg-current opacity-0 transition-opacity peer-checked:opacity-100"></span>
                                                </span>
                                                @if($q->type === 'scale_1_5')
                                                    <span class="font-syne text-sm font-bold">Nilai {{ $opt }}</span>
                                                    <span class="text-xs font-normal opacity-80">
                                                        @if($opt == 1) — Sangat Kurang / Tidak Puas
                                                        @elseif($opt == 2) — Kurang
                                                        @elseif($opt == 3) — Cukup
                                                        @elseif($opt == 4) — Baik / Puas
                                                        @elseif($opt == 5) — Sangat Baik / Sangat Puas
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="text-xs sm:text-sm font-medium">{{ $opt }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            @error("answers.{$q->id}")
                                <span class="text-xs text-rose-600 block font-medium mt-1">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    @endforeach

                    <!-- Feedback Tambahan / Saran Bebas -->
                    <div class="space-y-2">
                        <label for="additionalFeedback" class="block text-xs font-bold uppercase tracking-wider text-black">
                            Kritik, Saran & Masukan Tambahan untuk Dosen / Kelas (Opsional)
                        </label>
                        <textarea wire:model="additionalFeedback" id="additionalFeedback" rows="3"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-black focus:border-black transition-colors"
                            placeholder="Tuliskan pengalaman spesifik selama 3 bulan atau harapan Anda ke depan..."></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('feedbacks.index') }}" wire:navigate
                            class="px-5 py-3 text-xs font-bold text-slate-600 hover:text-black rounded-full transition-colors">
                            Batal
                        </a>
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-8 py-3.5 bg-black hover:bg-slate-900 text-white font-bold rounded-full text-xs sm:text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2 disabled:opacity-50">
                            <span wire:loading.remove>🚀 Kirim Jawaban Kuesioner</span>
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
            @endif
        </div>
    </div>
</div>
