<div class="space-y-8 font-space">
    <!-- Top Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-[#B5F938] text-black mb-2 uppercase">
                ⚙️ Area Administrator
            </span>
            <h1 class="font-syne text-3xl sm:text-4xl font-extrabold text-black tracking-tight">
                Statistik & Analitik Aspirasi
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Visualisasi data aduan masuk, tingkat penyelesaian, dan tren kategori kampus.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.feedbacks') }}" wire:navigate
                class="px-6 py-3 bg-black hover:bg-slate-900 text-white rounded-full text-xs sm:text-sm font-bold shadow-sm transition-colors flex items-center gap-2">
                <span>Kelola Semua Data</span>
                <svg class="w-4 h-4 text-[#B5F938]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>

    <!-- 4 Metrik Cards (Inspired by Moonli High-Contrast Bento Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Aspirasi (Solid Black) -->
        <div class="bg-black text-white p-6 rounded-[28px] shadow-sm flex flex-col justify-between">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Aspirasi</p>
            <p class="font-syne text-4xl font-extrabold text-[#B5F938] my-2">{{ $totalFeedbacks }}</p>
            <p class="text-[11px] text-slate-400">Dari {{ $totalUsers }} mahasiswa aktif</p>
        </div>

        <!-- Card 2: Pending (Off-White with Amber) -->
        <div class="bg-white p-6 rounded-[28px] border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <p class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Menunggu (Pending)</p>
            <p class="font-syne text-4xl font-extrabold text-black my-2">{{ $pendingFeedbacks }}</p>
            <p class="text-[11px] text-slate-500">Perlu verifikasi & disposisi</p>
        </div>

        <!-- Card 3: In Progress (Soft Gray Container) -->
        <div class="bg-[#ECEEE8] p-6 rounded-[28px] border border-black/5 shadow-xs flex flex-col justify-between">
            <p class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Sedang Diproses</p>
            <p class="font-syne text-4xl font-extrabold text-black my-2">{{ $inProgressFeedbacks }}</p>
            <p class="text-[11px] text-slate-600">Dalam koordinasi tim teknis</p>
        </div>

        <!-- Card 4: Resolved (Vibrant Lime Green) -->
        <div class="bg-[#B5F938] text-black p-6 rounded-[28px] shadow-sm flex flex-col justify-between">
            <p class="text-[11px] font-bold text-black uppercase tracking-wider">Tuntas Diselesaikan</p>
            <p class="font-syne text-4xl font-extrabold text-black my-2">{{ $resolvedFeedbacks }}</p>
            <p class="text-[11px] text-slate-800 font-semibold">Solusi resmi telah diterbitkan</p>
        </div>
    </div>

    <!-- Charts Section (Bar & Doughnut Charts dengan Palet Baru) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart 1: Aspirasi per Kategori -->
        <div class="bg-white p-6 sm:p-8 rounded-[32px] border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-syne text-lg font-bold text-black">Distribusi per Kategori</h3>
                    <p class="text-xs text-slate-500">Jumlah aduan di setiap sektor fasilitas & akademik</p>
                </div>
                <span class="text-[11px] font-bold px-3 py-1 bg-[#ECEEE8] text-black rounded-full">Bar Chart</span>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Proporsi Status Aspirasi -->
        <div class="bg-white p-6 sm:p-8 rounded-[32px] border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-syne text-lg font-bold text-black">Proporsi Status Penanganan</h3>
                    <p class="text-xs text-slate-500">Rasio tuntas vs pending dalam sistem</p>
                </div>
                <span class="text-[11px] font-bold px-3 py-1 bg-[#B5F938] text-black rounded-full">Doughnut</span>
            </div>
            <div class="relative h-64 w-full flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Urgent AI Aspirations Section -->
    @if($recentUrgent->count() > 0)
        <div class="bg-black text-white rounded-[32px] p-7 sm:p-8 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">🚨</span>
                    <div>
                        <h3 class="font-syne text-lg font-bold text-[#B5F938]">Prioritas Darurat (Deteksi AI)</h3>
                        <p class="text-xs text-slate-400">Aspirasi yang membutuhkan respon cepat menurut analisis AI Kataloka.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($recentUrgent as $urgent)
                    <div class="bg-[#18191B] p-5 rounded-2xl border border-white/10 flex flex-wrap items-center justify-between gap-4">
                        <div class="max-w-xl">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#B5F938] text-black">
                                    {{ strtoupper($urgent->urgency) }}
                                </span>
                                <span class="text-xs text-slate-400 font-semibold">{{ $urgent->category->name }}</span>
                            </div>
                            <h4 class="font-syne text-sm font-bold text-white">{{ $urgent->title }}</h4>
                            <p class="text-xs text-slate-400 line-clamp-1 mt-1">{{ $urgent->ai_summary ?? $urgent->description }}</p>
                        </div>
                        <a href="{{ route('feedbacks.detail', $urgent->id) }}" wire:navigate
                            class="px-4 py-2 bg-[#B5F938] hover:bg-[#a6f021] text-black rounded-full text-xs font-bold transition-all">
                            Tindak Lanjut →
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- SURVEY / KUESIONER EVALUASI MAHASISWA SECTION (Dapat Dibuat & Dihapus Kapanpun Oleh Admin) -->
    <div class="bg-white rounded-[32px] border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#B5F938] text-black uppercase">
                        📋 Manajemen Survei & Evaluasi
                    </span>
                    <span class="text-xs text-slate-500 font-bold">({{ $surveys->count() }} Form Survei)</span>
                </div>
                <h3 class="font-syne text-xl sm:text-2xl font-bold text-black">
                    Kuesioner Mutu Pengajar & Pengalaman Mahasiswa
                </h3>
                <p class="text-xs text-slate-500 mt-1">
                    Kelola form survei berkala (kualitas dosen, fasilitas kelas, pengalaman 3 bulan) yang dapat diisi oleh mahasiswa.
                </p>
            </div>

            <div>
                @if(!$showSurveyForm)
                    <button type="button" wire:click="openCreateSurveyForm"
                        class="px-5 py-2.5 bg-black hover:bg-slate-900 text-white rounded-full text-xs font-bold transition-all flex items-center gap-2 shadow-sm">
                        <span class="text-[#B5F938] text-base leading-none">+</span>
                        <span>Buat Form Survei Baru</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Form Tambah Survei Baru (Collapsible / Modal Inline) -->
        @if($showSurveyForm)
            <div class="p-6 sm:p-8 rounded-[28px] bg-[#F7F8F5] border-2 border-black/10 space-y-6 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-syne text-lg font-bold text-black flex items-center gap-2">
                            <span>✨</span> Buat Form Kuesioner Baru
                        </h4>
                        <p class="text-xs text-slate-500">Tentukan pertanyaan pilihan ganda untuk mengevaluasi kepuasan mahasiswa.</p>
                    </div>
                    <button type="button" wire:click="closeCreateSurveyForm" class="text-xs font-bold text-slate-500 hover:text-black">
                        ✕ Tutup Form
                    </button>
                </div>

                <form wire:submit="saveSurvey" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-black mb-1">
                                Judul Form Survei <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" wire:model="surveyTitle" required
                                placeholder="Contoh: Evaluasi Kualitas Pengajar & Pengalaman 3 Bulan Belajar"
                                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-black">
                            @error('surveyTitle') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-black mb-1">
                                Sasaran Mahasiswa
                            </label>
                            <input type="text" wire:model="surveyTargetAudience"
                                placeholder="Contoh: Mahasiswa Semester 1 & 3"
                                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-black">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-black mb-1">
                            Deskripsi / Pengantar Survei
                        </label>
                        <textarea wire:model="surveyDescription" rows="2"
                            placeholder="Jelaskan tujuan kuesioner ini agar mahasiswa berpartisipasi secara jujur dan objektif..."
                            class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-black"></textarea>
                    </div>

                    <!-- Dynamic Questions Builder -->
                    <div class="space-y-4 pt-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold uppercase tracking-wider text-black">
                                Daftar Pertanyaan Kuesioner (Pilihan Ganda)
                            </label>
                            <button type="button" wire:click="addQuestion"
                                class="text-xs font-bold text-black bg-[#B5F938] hover:bg-[#a5f123] px-3.5 py-1.5 rounded-full transition-all">
                                + Tambah Pertanyaan Lain
                            </button>
                        </div>

                        @foreach($surveyQuestions as $index => $q)
                            <div class="p-4 bg-white rounded-2xl border border-slate-200 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-black flex items-center gap-1.5">
                                        <span class="w-5 h-5 rounded-full bg-black text-[#B5F938] flex items-center justify-center text-[10px] font-bold">
                                            {{ $index + 1 }}
                                        </span>
                                        Pertanyaan #{{ $index + 1 }}
                                    </span>

                                    @if(count($surveyQuestions) > 1)
                                        <button type="button" wire:click="removeQuestion({{ $index }})"
                                            class="text-[11px] font-bold text-rose-600 hover:text-rose-800">
                                            ✕ Hapus
                                        </button>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="sm:col-span-2">
                                        <input type="text" wire:model="surveyQuestions.{{ $index }}.question" required
                                            placeholder="Tuliskan pertanyaan evaluasi..."
                                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium focus:ring-black">
                                        @error("surveyQuestions.{$index}.question")
                                            <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <select wire:model="surveyQuestions.{{ $index }}.type"
                                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold focus:ring-black">
                                            <option value="scale_1_5">Nilai Rating 1 - 5 (Skala Angka)</option>
                                            <option value="satisfaction_4">Skala 4: Tidak Puas - Sangat Puas</option>
                                            <option value="satisfaction_5">Skala 5: Sangat Tidak Puas - Sangat Puas</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="closeCreateSurveyForm"
                            class="px-5 py-2.5 text-xs font-bold text-slate-600 hover:text-black">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-6 py-2.5 bg-black hover:bg-slate-900 text-white font-bold rounded-full text-xs shadow-sm transition-all disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveSurvey">🚀 Terbitkan Form Survei</span>
                            <span wire:loading wire:target="saveSurvey">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Daftar Survei yang Ada -->
        <div class="space-y-4">
            @forelse($surveys as $survey)
                <div class="p-6 rounded-[28px] border border-slate-200/90 hover:border-black transition-all bg-[#FDFDFD] space-y-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="space-y-1 max-w-xl">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $survey->is_active ? 'bg-[#B5F938] text-black' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $survey->is_active ? '● Aktif (Ditampilkan)' : '✕ Ditutup' }}
                                </span>
                                @if($survey->target_audience)
                                    <span class="text-[11px] font-bold text-slate-500 bg-[#ECEEE8] px-2.5 py-0.5 rounded-full">
                                        {{ $survey->target_audience }}
                                    </span>
                                @endif
                                <span class="text-[11px] text-slate-400">Dibuat {{ $survey->created_at->diffForHumans() }}</span>
                            </div>

                            <h4 class="font-syne text-lg font-bold text-black leading-snug">
                                {{ $survey->title }}
                            </h4>

                            @if($survey->description)
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $survey->description }}</p>
                            @endif
                        </div>

                        <!-- Action Controls Admin -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('surveys.fill', $survey->id) }}" target="_blank"
                                class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-[#ECEEE8] hover:bg-black hover:text-white text-black transition-colors"
                                title="Buka form tampilan mahasiswa">
                                👁️ Buka Form
                            </a>

                            <button type="button" wire:click="toggleSurveyStatus({{ $survey->id }})"
                                class="px-3.5 py-1.5 rounded-full text-xs font-bold border border-slate-300 hover:border-black text-slate-700 transition-colors">
                                {{ $survey->is_active ? 'Tutup Survei' : 'Aktifkan' }}
                            </button>

                            <button type="button" wire:click="deleteSurvey({{ $survey->id }})"
                                wire:confirm="PERINGATAN: Apakah Anda yakin ingin menghapus form survei ini beserta seluruh data respon yang telah diisi mahasiswa?"
                                class="px-3.5 py-1.5 rounded-full text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-600 hover:text-white border border-rose-200 transition-colors"
                                title="Hapus Form Survei Kapanpun">
                                🗑️ Hapus
                            </button>
                        </div>
                    </div>

                    <!-- Metrics Survei: Responden & Skor Rata-rata -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                        <div class="p-3 bg-white rounded-2xl border border-slate-100 shadow-2xs">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Responden</p>
                            <p class="font-syne text-xl font-bold text-black mt-0.5">{{ $survey->responses->count() }} Mahasiswa</p>
                        </div>

                        <div class="p-3 bg-white rounded-2xl border border-slate-100 shadow-2xs">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Rata-Rata Kepuasan</p>
                            <p class="font-syne text-xl font-bold text-black mt-0.5">
                                {{ $survey->average_score > 0 ? $survey->average_score . ' / 5.0 ⭐' : 'Belum Ada Skor' }}
                            </p>
                        </div>

                        <div class="p-3 bg-white rounded-2xl border border-slate-100 shadow-2xs">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Jumlah Pertanyaan</p>
                            <p class="font-syne text-xl font-bold text-black mt-0.5">{{ $survey->questions->count() }} Butir</p>
                        </div>

                        <div class="p-3 bg-white rounded-2xl border border-slate-100 shadow-2xs">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Status Akses</p>
                            <p class="font-syne text-sm font-bold {{ $survey->is_active ? 'text-black' : 'text-slate-400' }} mt-1">
                                {{ $survey->is_active ? 'Terbuka untuk Mahasiswa' : 'Pengisian Ditutup' }}
                            </p>
                        </div>
                    </div>

                    <!-- Rincian Butir Pertanyaan -->
                    <div class="pt-2 border-t border-slate-100">
                        <details class="group cursor-pointer">
                            <summary class="text-xs font-bold text-slate-600 hover:text-black flex items-center justify-between py-1">
                                <span>Lihat Rincian Butir Pertanyaan & Rata-rata Penilaian ({{ $survey->questions->count() }} Pertanyaan)</span>
                                <span class="group-open:rotate-180 transition-transform">▼</span>
                            </summary>
                            <div class="mt-3 space-y-2.5 pt-2">
                                @foreach($survey->questions as $qIdx => $question)
                                    @php
                                        $avgQ = round((float) $question->answers()->whereNotNull('score')->avg('score'), 2);
                                        $ansCount = $question->answers()->count();
                                    @endphp
                                    <div class="p-3 bg-white rounded-xl border border-slate-100 flex flex-wrap items-center justify-between text-xs gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-full bg-[#ECEEE8] text-black font-bold text-[10px] flex items-center justify-center">
                                                {{ $qIdx + 1 }}
                                            </span>
                                            <span class="font-medium text-slate-800">{{ $question->question }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-[11px] text-slate-400">{{ $ansCount }} jawaban</span>
                                            <span class="font-bold text-black bg-[#B5F938] px-2.5 py-0.5 rounded-full text-[11px]">
                                                {{ $avgQ > 0 ? "Rata-rata: {$avgQ} / 5" : "Belum dinilai" }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center bg-[#F7F8F5] rounded-3xl border border-dashed border-slate-300">
                    <p class="text-sm font-bold text-slate-700">Belum Ada Form Survei Evaluasi</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                        Admin dapat menambahkan form kuesioner kapanpun untuk mengumpulkan feedback kepuasan mahasiswa.
                    </p>
                    <button type="button" wire:click="openCreateSurveyForm"
                        class="mt-4 px-5 py-2.5 bg-black text-[#B5F938] font-bold rounded-full text-xs hover:bg-slate-900 transition-colors">
                        + Buat Form Survei Pertama
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Scripts for Chart.js initialization (Support both direct load & wire:navigate) -->
    <script>
        function initKatalokaCharts() {
            if (typeof Chart === 'undefined') {
                return;
            }

            // 1. Category Chart
            const catCtx = document.getElementById('categoryChart');
            if (catCtx) {
                const existingCat = Chart.getChart(catCtx);
                if (existingCat) {
                    existingCat.destroy();
                }

                new Chart(catCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! $categoryLabels !!},
                        datasets: [{
                            label: 'Jumlah Aspirasi',
                            data: {!! $categoryCounts !!},
                            backgroundColor: '#111215',
                            hoverBackgroundColor: '#B5F938',
                            borderRadius: 12,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            }

            // 2. Status Doughnut Chart
            const statusCtx = document.getElementById('statusChart');
            if (statusCtx) {
                const existingStatus = Chart.getChart(statusCtx);
                if (existingStatus) {
                    existingStatus.destroy();
                }

                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! $statusLabels !!},
                        datasets: [{
                            data: {!! $statusCounts !!},
                            backgroundColor: ['#f59e0b', '#111215', '#B5F938', '#f43f5e'],
                            borderWidth: 0,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', initKatalokaCharts);
        document.addEventListener('livewire:navigated', initKatalokaCharts);
    </script>
</div>
