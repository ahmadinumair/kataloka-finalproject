<div class="space-y-12 relative">
    <!-- Top Instant Loading Indicator (Lime Bar during Livewire updates) -->
    <div wire:loading.delay.shortest class="fixed top-0 left-0 right-0 h-1 bg-[#B5F938] z-50 animate-pulse pointer-events-none shadow-[0_0_10px_#B5F938]"></div>

    <!-- Hero Section (Inspired by Moonli Lime Hero Box) -->
    <div class="relative bg-[#B5F938] text-black rounded-[36px] p-8 sm:p-14 overflow-hidden border border-black/10 shadow-sm">
        <!-- Minimal geometric decorative circles/lines -->
        <div class="absolute -right-12 -top-12 w-64 h-64 rounded-full border border-black/15 pointer-events-none"></div>
        <div class="absolute right-24 bottom-6 w-32 h-32 rounded-full border border-black/15 pointer-events-none"></div>
        <div class="absolute right-12 top-1/2 -translate-y-1/2 hidden lg:flex flex-col gap-3 opacity-90 pointer-events-none">
            <div class="w-48 p-4 bg-black text-white rounded-2xl text-xs font-space">
                <span class="text-[#B5F938] font-bold">● AI Analysis</span>
                <p class="text-[11px] text-slate-300 mt-1">Ekstraksi poin inti & deteksi urgensi otomatis.</p>
            </div>
            <div class="w-48 p-4 bg-white/90 text-black rounded-2xl text-xs font-space border border-black/10">
                <span class="font-bold">✓ Status Terbuka</span>
                <p class="text-[11px] text-slate-600 mt-1">Pantau progres penanganan 3 tahap real-time.</p>
            </div>
        </div>

        <div class="relative z-10 max-w-2xl">
            <!-- Tiny pill tag -->
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-black text-white text-[11px] font-space font-bold tracking-wide uppercase mb-6">
                <span class="w-2 h-2 rounded-full bg-[#B5F938] animate-pulse"></span>
                Kataloka AI Feedback Platform
            </div>

            <!-- Huge Head Title with 'Syne' font -->
            <h1 class="font-syne text-3xl sm:text-5xl font-extrabold tracking-tight leading-[1.15] text-black">
                Suarakan aspirasi, pantau solusi nyata untuk kampus kita.
            </h1>

            <p class="font-space text-slate-800 text-sm sm:text-base mt-4 max-w-xl leading-relaxed">
                Platform pengaduan independen mahasiswa dengan analisis cerdas AI. Dari masalah fasilitas hingga akademik, setiap suara langsung terdata dan ditindaklanjuti.
            </p>

            <!-- Action CTA button (Pill Shape Black like Reference) -->
            <div class="flex flex-wrap items-center gap-3 mt-8">
                <a href="{{ auth()->check() ? route('feedbacks.create') : route('login') }}" wire:navigate
                    class="px-7 py-3.5 bg-black hover:bg-slate-900 text-white font-space font-bold rounded-full text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2.5">
                    <span>+ Tulis Aspirasi Baru</span>
                    <svg class="w-4 h-4 text-[#B5F938]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>

        <!-- 3 Quick Metric Counters inside Lime Hero -->
        <div class="mt-12 pt-8 border-t border-black/15 grid grid-cols-3 gap-4 text-left font-space">
            <div>
                <p class="font-syne text-3xl sm:text-4xl font-extrabold text-black">{{ $totalCount }}</p>
                <p class="text-xs font-semibold text-slate-800 mt-0.5">Total Aspirasi Masuk</p>
            </div>
            <div>
                <p class="font-syne text-3xl sm:text-4xl font-extrabold text-black">{{ $pendingCount }}</p>
                <p class="text-xs font-semibold text-slate-800 mt-0.5">Menunggu Verifikasi</p>
            </div>
            <div>
                <p class="font-syne text-3xl sm:text-4xl font-extrabold text-black">{{ $resolvedCount }}</p>
                <p class="text-xs font-semibold text-slate-800 mt-0.5">Tuntas Diselesaikan</p>
            </div>
        </div>
    </div>

    <!-- Category Carousel / Tiles Row (Inspired by 'Networks' pill strip in reference) -->
    <div class="bg-[#ECEEE8] rounded-[32px] p-6 sm:p-8 border border-black/5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-xs">
                <h3 class="font-syne text-2xl font-extrabold text-black tracking-tight">Kategori</h3>
                <p class="text-xs font-space text-slate-600 mt-1">
                    Pilih bidang aduan yang ingin Anda jelajahi atau sampaikan.
                </p>
            </div>

            <!-- Category Pills Grid -->
            <div class="flex flex-wrap items-center gap-2.5 flex-1 justify-start lg:justify-end">
                <button type="button" wire:click="$set('category', '')" wire:loading.attr="disabled"
                    class="px-4 py-2.5 rounded-2xl text-xs font-space font-bold transition-all disabled:opacity-60 {{ empty($category) ? 'bg-black text-white shadow-sm' : 'bg-white hover:bg-slate-50 text-slate-800 border border-black/5' }}">
                    Semua Kategori
                </button>
                @foreach($categories as $cat)
                    <button type="button" wire:click="$set('category', '{{ $cat->id }}')" wire:loading.attr="disabled"
                        class="px-4 py-2.5 rounded-2xl text-xs font-space font-bold transition-all disabled:opacity-60 {{ $category == $cat->id ? 'bg-[#B5F938] text-black shadow-sm ring-2 ring-black' : 'bg-white hover:bg-slate-50 text-slate-800 border border-black/5' }}">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Statement with Highlight Marker (Inspired by "...networks we support meticulously...") -->
    <div class="text-center py-4">
        <h2 class="font-syne text-2xl sm:text-3xl font-extrabold text-black tracking-tight max-w-2xl mx-auto leading-snug">
            Kita kawal setiap masukan yang <mark class="bg-[#B5F938] text-black px-2.5 py-0.5 rounded-lg inline-block">diprioritaskan</mark> bersama hingga tuntas.
        </h2>
    </div>

    <!-- Active Campus Survey Bento Banner (Fitur Form Evaluasi Mahasiswa) -->
    @if($activeSurvey)
        <div class="bg-black text-white rounded-[32px] p-6 sm:p-8 border border-white/10 shadow-sm font-space">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2 max-w-2xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-[#B5F938] text-black uppercase">
                            📋 Kuesioner Evaluasi Mahasiswa
                        </span>
                        @if($activeSurvey->target_audience)
                            <span class="text-[11px] font-bold text-slate-300 bg-white/10 px-2.5 py-0.5 rounded-full">
                                {{ $activeSurvey->target_audience }}
                            </span>
                        @endif
                        <span class="text-[11px] text-slate-400">{{ $activeSurvey->responses->count() }} Mahasiswa Mengisi</span>
                    </div>

                    <h3 class="font-syne text-xl sm:text-2xl font-bold text-white tracking-tight">
                        {{ $activeSurvey->title }}
                    </h3>

                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        {{ $activeSurvey->description ?? 'Admin kampus membuka form survei evaluasi mutu pengajaran dosen dan kenyamanan ruang kelas.' }}
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    @auth
                        @if($activeSurvey->isCompletedBy(auth()->user()))
                            <span class="inline-flex items-center gap-1.5 px-5 py-3 rounded-full text-xs font-bold bg-white/10 text-[#B5F938] border border-[#B5F938]/30">
                                <span>✓</span>
                                <span>Sudah Anda Isi</span>
                            </span>
                        @else
                            <a href="{{ route('surveys.fill', $activeSurvey->id) }}" wire:navigate
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-xs font-bold bg-[#B5F938] hover:bg-[#a6f021] text-black shadow-md hover:shadow-lg transition-all">
                                <span>Isi Kuesioner Sekarang</span>
                                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" wire:navigate
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-xs font-bold bg-[#B5F938] hover:bg-[#a6f021] text-black shadow-md transition-all">
                            <span>Masuk untuk Mengisi</span>
                            <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    @endif

    <!-- Filter & Search Bar Section -->
    <div class="bg-white rounded-[28px] p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4 font-space">
        <!-- Top tab bar: Semua vs Aspirasi Saya -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="inline-flex p-1 bg-slate-100 rounded-full">
                <button type="button" wire:click="$set('tab', 'all')"
                    class="px-5 py-2 rounded-full text-xs font-bold transition-all {{ $tab === 'all' ? 'bg-black text-white shadow-sm' : 'text-slate-600 hover:text-black' }}">
                    Semua Aspirasi Kampus
                </button>
                @auth
                    <button type="button" wire:click="$set('tab', 'my')"
                        class="px-5 py-2 rounded-full text-xs font-bold transition-all {{ $tab === 'my' ? 'bg-black text-white shadow-sm' : 'text-slate-600 hover:text-black' }}">
                        Aspirasi Saya
                    </button>
                @endauth
            </div>

            @if(!empty($search) || !empty($category) || !empty($type) || !empty($status) || $sort !== 'latest')
                <button type="button" wire:click="resetFilters" class="text-xs font-bold text-rose-600 hover:underline">
                    ↺ Reset Semua Filter
                </button>
            @endif
        </div>

        <!-- Filter Controls -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <!-- Live Search -->
            <div class="lg:col-span-2 relative">
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Cari kata kunci masalah, gedung, atau fasilitas..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:ring-2 focus:ring-black focus:border-black transition-colors">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <!-- Status Filter -->
            <div>
                <select wire:model.live="status"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:ring-2 focus:ring-black focus:border-black">
                    <option value="">Semua Status</option>
                    <option value="pending">⏳ Pending</option>
                    <option value="in_progress">⚙️ Sedang Diproses</option>
                    <option value="resolved">✅ Tuntas Diselesaikan</option>
                    <option value="rejected">❌ Ditolak</option>
                </select>
            </div>

            <!-- Sort Option -->
            <div>
                <select wire:model.live="sort"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:ring-2 focus:ring-black focus:border-black">
                    <option value="latest">Terbaru Ditambahkan</option>
                    <option value="popular">Dukungan Terbanyak 🔥</option>
                    <option value="urgent">Prioritas / Urgensi AI ⚡</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Feedbacks Cards Grid (Inspired by Clean Bento Cards in Reference) -->
    <div wire:loading.class="opacity-50" wire:target="category, search, status, sort, tab"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 font-space transition-opacity duration-150">
        @forelse($feedbacks as $fb)
            <div class="bg-white rounded-[28px] border border-slate-200/80 p-6 hover:border-black transition-all duration-300 flex flex-col justify-between group shadow-2xs">
                <div>
                    <!-- Top Pills: Category + Status -->
                    <div class="flex items-center justify-between gap-2 mb-3.5">
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-[#ECEEE8] text-black">
                                {{ $fb->category->name }}
                            </span>
                            @if($fb->image_path)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-black text-[#B5F938]" title="Terdapat Bukti Foto">
                                    📷 Foto
                                </span>
                            @endif
                        </div>

                        <!-- Status Badge -->
                        @if($fb->status === 'resolved')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold bg-[#B5F938] text-black">
                                ✓ Selesai
                            </span>
                        @elseif($fb->status === 'in_progress')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-900">
                                ⚙ Diproses
                            </span>
                        @elseif($fb->status === 'rejected')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                                ✕ Ditolak
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                ⏳ Pending
                            </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h3 class="font-syne text-lg font-bold text-black group-hover:text-slate-700 transition-colors line-clamp-2 leading-tight">
                        <a href="{{ route('feedbacks.detail', $fb->id) }}" wire:navigate>
                            {{ $fb->title }}
                        </a>
                    </h3>

                    <!-- Urgency Tag if High / Urgent -->
                    @if(in_array($fb->urgency, ['high', 'urgent']))
                        <div class="mt-2.5">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold font-space {{ $fb->urgency === 'urgent' ? 'bg-black text-[#B5F938]' : 'bg-amber-100 text-amber-900' }}">
                                ⚡ Urgensi AI: {{ ucfirst($fb->urgency) }}
                            </span>
                        </div>
                    @endif

                    <!-- Short Description -->
                    <p class="text-xs text-slate-600 mt-2.5 line-clamp-3 leading-relaxed">
                        {{ $fb->description }}
                    </p>

                    <!-- AI Summary Box -->
                    @if($fb->ai_summary)
                        <div class="mt-4 p-3.5 rounded-2xl bg-[#F7F8F5] border border-black/5 text-[11px] text-slate-800">
                            <span class="font-bold text-black flex items-center gap-1 mb-1">
                                🤖 Inti Masalah (AI):
                            </span>
                            <p class="line-clamp-2 italic text-slate-700">{{ $fb->ai_summary }}</p>
                        </div>
                    @endif
                </div>

                <!-- Footer: User, Responses, and Upvote Button -->
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <div>
                        <p class="font-bold text-black text-xs">
                            {{ $fb->is_anonymous ? '👤 Anonim' : $fb->user->name }}
                        </p>
                        <p class="text-[10px] text-slate-400">{{ $fb->created_at->diffForHumans() }}</p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <!-- Responses count -->
                        @if($fb->responses_count > 0)
                            <span class="inline-flex items-center gap-1 text-black font-bold text-xs" title="Sudah ditanggapi admin">
                                💬 {{ $fb->responses_count }}
                            </span>
                        @endif

                        <!-- Upvote Button -->
                        <button type="button" wire:click="toggleUpvote({{ $fb->id }})"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition-all
                            {{ $fb->isUpvotedBy(auth()->user()) ? 'bg-black text-[#B5F938] shadow-sm' : 'bg-slate-100 text-slate-800 hover:bg-[#B5F938] hover:text-black' }}">
                            <svg class="w-3.5 h-3.5" fill="{{ $fb->isUpvotedBy(auth()->user()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                            </svg>
                            <span>{{ $fb->upvotes_count }}</span>
                        </button>

                        @auth
                            @if(auth()->id() === $fb->user_id || auth()->user()->role === 'admin')
                                <button type="button" wire:click="deleteFeedback({{ $fb->id }})"
                                    wire:confirm="{{ auth()->id() === $fb->user_id ? 'Apakah Anda yakin ingin membatalkan/menghapus aspirasi ini?' : 'Admin: Hapus aspirasi ini dari publik?' }}"
                                    class="p-1.5 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                    title="{{ auth()->id() === $fb->user_id ? 'Batalkan Aspirasi Saya' : 'Hapus Aspirasi (Admin)' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-[32px] border border-slate-200">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#ECEEE8] flex items-center justify-center text-black font-syne text-xl font-bold">
                    !
                </div>
                <h3 class="font-syne text-lg font-bold text-black">Belum Ada Aspirasi Ditemukan</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Tidak ada aspirasi yang sesuai dengan kata kunci atau filter terpilih.</p>
                <button type="button" wire:click="resetFilters" class="mt-4 px-5 py-2.5 bg-black text-white rounded-full text-xs font-bold hover:bg-slate-800 transition-colors">
                    Reset Filter
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $feedbacks->links() }}
    </div>

    <!-- Bottom Bento Feature Highlights (Inspired by 3 Bento boxes in reference image) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 font-space">
        <!-- Box 1: Solid Black Card -->
        <div class="bg-black text-white rounded-[32px] p-7 flex flex-col justify-between shadow-sm">
            <div>
                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold bg-[#B5F938] text-black uppercase mb-4">
                    Kerahasiaan Terjamin
                </span>
                <h4 class="font-syne text-xl font-bold leading-snug">Fitur Mode Anonim Pelapor</h4>
                <p class="text-xs text-slate-300 mt-2 leading-relaxed">
                    Kirim kritik dan keluhan tanpa rasa cemas. Identitas Anda dapat disembunyikan sepenuhnya dari publik.
                </p>
            </div>
            <div class="mt-6 text-right">
                <span class="text-2xl">🔒</span>
            </div>
        </div>

        <!-- Box 2: Lime Green Card -->
        <div class="bg-[#B5F938] text-black rounded-[32px] p-7 flex flex-col justify-between shadow-sm">
            <div>
                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold bg-black text-white uppercase mb-4">
                    Kecerdasan Buatan
                </span>
                <h4 class="font-syne text-xl font-bold leading-snug">Rangkuman Instan Berbasis AI</h4>
                <p class="text-xs text-slate-800 mt-2 leading-relaxed">
                    Sistem otomatis mengekstrak inti masalah, lokasi kejadian, dan tingkat urgensi demi prioritas penanganan cepat.
                </p>
            </div>
            <div class="mt-6 text-right">
                <span class="text-2xl">⚡</span>
            </div>
        </div>

        <!-- Box 3: Clean Off-White Card -->
        <div class="bg-white text-black rounded-[32px] p-7 flex flex-col justify-between border border-slate-200/80 shadow-sm">
            <div>
                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold bg-slate-200 text-slate-800 uppercase mb-4">
                    Transparansi
                </span>
                <h4 class="font-syne text-xl font-bold leading-snug">Tracking Linimasa Terbuka</h4>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                    Pantau status penyelesaian aduan Anda langkah demi langkah hingga tindak lanjut resmi dari pihak kampus keluar.
                </p>
            </div>
            <div class="mt-6 text-right">
                <span class="text-2xl">📈</span>
            </div>
        </div>
    </div>
</div>
