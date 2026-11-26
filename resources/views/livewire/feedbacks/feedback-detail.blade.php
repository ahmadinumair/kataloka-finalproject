<div class="max-w-4xl mx-auto my-6 space-y-6 font-space">
    <!-- Back button -->
    <div>
        <a href="{{ route('feedbacks.index') }}" wire:navigate class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-black transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Semua Aspirasi
        </a>
    </div>

    <!-- Main Feedback Card -->
    <div class="bg-white rounded-[36px] border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="p-6 sm:p-10 border-b border-slate-100">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-2">
                    <span class="px-3.5 py-1 rounded-full text-xs font-bold bg-[#ECEEE8] text-black">
                        {{ $feedback->category->name }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                        {{ $feedback->feedbackType->name }}
                    </span>
                    @if(in_array($feedback->urgency, ['high', 'urgent']))
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $feedback->urgency === 'urgent' ? 'bg-black text-[#B5F938]' : 'bg-amber-100 text-amber-900' }}">
                            ⚡ Urgensi AI: {{ ucfirst($feedback->urgency) }}
                        </span>
                    @endif
                </div>

                <!-- Actions: Upvote & Delete -->
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="toggleUpvote"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold transition-all
                        {{ $feedback->isUpvotedBy(auth()->user()) ? 'bg-black text-[#B5F938] shadow-sm' : 'bg-slate-100 text-slate-800 hover:bg-[#B5F938] hover:text-black' }}">
                        <svg class="w-4 h-4" fill="{{ $feedback->isUpvotedBy(auth()->user()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                        </svg>
                        <span>Dukung Aspirasi ({{ $feedback->upvotes->count() }})</span>
                    </button>

                    @auth
                        @if(auth()->id() === $feedback->user_id)
                            <button type="button" wire:click="deleteFeedback"
                                wire:confirm="Apakah Anda yakin ingin membatalkan dan menghapus aspirasi ini secara permanen?"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors"
                                title="Batalkan Aspirasi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                <span>Batalkan Aspirasi</span>
                            </button>
                        @endif
                    @endauth
                </div>
            </div>

            <h1 class="font-syne text-2xl sm:text-4xl font-extrabold text-black tracking-tight leading-snug">
                {{ $feedback->title }}
            </h1>

            <div class="mt-4 flex items-center gap-3 text-xs text-slate-500">
                <span class="font-bold text-black">Oleh: {{ $feedback->is_anonymous ? '👤 Mahasiswa (Anonim)' : $feedback->user->name }}</span>
                <span>•</span>
                <span>Diajukan: {{ $feedback->created_at->translatedFormat('d F Y, H:i') }} ({{ $feedback->created_at->diffForHumans() }})</span>
            </div>
        </div>

        <!-- Timeline Status Stepper (Styled with Lime/Black Accents) -->
        <div class="bg-[#ECEEE8] p-6 sm:p-8 border-b border-black/5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Linimasa Progres Tindak Lanjut</h3>
            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <!-- Step 1: Terkirim -->
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold mb-1.5 bg-black text-[#B5F938] shadow-sm">
                        ✓
                    </div>
                    <span class="font-bold text-black">Terkirim</span>
                    <span class="text-[10px] text-slate-500">Masuk antrean</span>
                </div>

                <!-- Step 2: Sedang Diproses -->
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold mb-1.5
                        {{ in_array($feedback->status, ['in_progress', 'resolved']) ? 'bg-black text-[#B5F938]' : ($feedback->status === 'rejected' ? 'bg-slate-300 text-slate-500' : 'bg-amber-400 text-black ring-4 ring-amber-200 animate-pulse') }}">
                        {{ in_array($feedback->status, ['in_progress', 'resolved']) ? '✓' : '2' }}
                    </div>
                    <span class="font-bold {{ in_array($feedback->status, ['in_progress', 'resolved']) ? 'text-black' : 'text-slate-500' }}">Sedang Diproses</span>
                    <span class="text-[10px] text-slate-500">Pihak kampus menindak</span>
                </div>

                <!-- Step 3: Tuntas Diselesaikan -->
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold mb-1.5
                        {{ $feedback->status === 'resolved' ? 'bg-[#B5F938] text-black ring-2 ring-black font-extrabold shadow-sm' : ($feedback->status === 'rejected' ? 'bg-rose-600 text-white' : 'bg-slate-300 text-slate-500') }}">
                        {{ $feedback->status === 'resolved' ? '✓' : ($feedback->status === 'rejected' ? '✕' : '3') }}
                    </div>
                    <span class="font-bold {{ $feedback->status === 'resolved' ? 'text-black' : ($feedback->status === 'rejected' ? 'text-rose-700' : 'text-slate-500') }}">
                        {{ $feedback->status === 'rejected' ? 'Ditolak' : 'Tuntas / Selesai' }}
                    </span>
                    <span class="text-[10px] text-slate-500">Solusi diberikan</span>
                </div>
            </div>
        </div>

        <!-- Description Body -->
        <div class="p-6 sm:p-10 space-y-6">
            <!-- AI Summary Card -->
            @if($feedback->ai_summary)
                <div class="p-6 rounded-[28px] bg-black text-white border border-black/10">
                    <div class="flex items-center gap-2 mb-2 text-[#B5F938] font-bold text-xs uppercase tracking-wider">
                        <span>🤖</span> Rangkuman Cerdas AI Kataloka
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-slate-200 leading-relaxed italic">
                        "{{ $feedback->ai_summary }}"
                    </p>
                </div>
            @endif

            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Deskripsi Lengkap Aspirasi</h3>
                <div class="text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line bg-[#F7F8F5] p-6 rounded-[24px] border border-slate-200/60">
                    {{ $feedback->description }}
                </div>
            </div>

            <!-- Foto Bukti / Lampiran (Jika Ada) -->
            @if($feedback->image_path)
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Foto Bukti / Lampiran Aduan</h3>
                    <div class="rounded-[28px] overflow-hidden border border-black/10 bg-slate-100 max-w-xl shadow-xs">
                        <a href="{{ asset('storage/' . $feedback->image_path) }}" target="_blank" class="block group relative">
                            <img src="{{ asset('storage/' . $feedback->image_path) }}" alt="{{ $feedback->title }}" class="w-full max-h-96 object-cover group-hover:scale-102 transition-transform duration-300">
                            <div class="absolute bottom-3 right-3 bg-black/80 text-white text-[11px] font-space font-bold px-3 py-1.5 rounded-full backdrop-blur-xs flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                <span>Lihat Ukuran Penuh</span>
                            </div>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Admin Quick Controls (Khusus Admin) -->
    @auth
        @if(auth()->user()->role === 'admin')
            <div class="bg-[#ECEEE8] border border-black/5 rounded-[28px] p-6 shadow-sm">
                <h3 class="text-sm font-syne font-bold text-black mb-3 flex items-center gap-2">
                    🛠️ Kontrol Cepat Admin Kampus
                </h3>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs text-slate-600 font-bold">Ubah Status Tiket:</span>
                    <button type="button" wire:click="updateStatus('in_progress')"
                        class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-400 hover:bg-amber-500 text-black transition-colors">
                        ⚙️ Sedang Diproses
                    </button>
                    <button type="button" wire:click="updateStatus('resolved')"
                        class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-[#B5F938] hover:bg-[#a0f215] text-black transition-colors">
                        ✅ Selesai / Tuntas
                    </button>
                    <button type="button" wire:click="updateStatus('rejected')"
                        class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white transition-colors">
                        ❌ Ditolak
                    </button>
                    <button type="button" wire:click="updateStatus('pending')"
                        class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-800 hover:bg-black text-white transition-colors">
                        ⏳ Kembalikan ke Pending
                    </button>
                    <div class="h-4 w-[1px] bg-slate-300 mx-1"></div>
                    <button type="button" wire:click="deleteFeedback"
                        wire:confirm="PERINGATAN ADMIN: Apakah Anda yakin ingin menghapus seluruh aspirasi ini dari publik beserta tanggapannya?"
                        class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white transition-colors flex items-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        <span>Hapus Aspirasi (Admin)</span>
                    </button>
                </div>
            </div>
        @endif
    @endauth

    <!-- Tanggapan & Klarifikasi Resmi Admin Section -->
    <div class="bg-white rounded-[36px] border border-slate-200/80 shadow-sm p-6 sm:p-10 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="font-syne text-xl font-bold text-black flex items-center gap-2">
                💬 Tanggapan Resmi Pihak Kampus
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-[#B5F938] text-black">
                    {{ $feedback->responses->count() }} Respon
                </span>
            </h2>
        </div>

        <!-- List of Responses -->
        <div class="space-y-4">
            @forelse($feedback->responses as $resp)
                <div class="p-6 rounded-[24px] bg-[#F7F8F5] border border-slate-200/80 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-black text-[#B5F938] font-bold flex items-center justify-center text-xs">
                                A
                            </div>
                            <div>
                                <span class="font-bold text-black">{{ $resp->admin->name }}</span>
                                <span class="ml-1 text-[10px] px-2 py-0.5 bg-[#B5F938] text-black rounded-full font-bold uppercase">Admin Kampus</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-slate-400">{{ $resp->created_at->translatedFormat('d M Y, H:i') }}</span>
                            @if(auth()->check() && auth()->user()->role === 'admin')
                                <button type="button" wire:click="deleteResponse({{ $resp->id }})"
                                    wire:confirm="Apakah Anda yakin ingin menghapus tanggapan resmi ini?"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-200 transition-colors"
                                    title="Hapus Tanggapan">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Hapus</span>
                                </button>
                            @endif
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-800 whitespace-pre-line leading-relaxed pl-10">
                        {{ $resp->message }}
                    </p>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 text-xs">
                    <p>Belum ada tanggapan resmi dari pihak kampus untuk aspirasi ini.</p>
                </div>
            @endforelse
        </div>

        <!-- Form Tambah Tanggapan Resmi (Khusus Admin) -->
        @auth
            @if(auth()->user()->role === 'admin')
                <div class="pt-6 border-t border-slate-100">
                    <h4 class="font-syne text-base font-bold text-black mb-2">Tulis Tanggapan Resmi / Solusi</h4>
                    <form wire:submit="postAdminResponse" class="space-y-3">
                        <textarea wire:model="adminResponse" rows="4" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-black focus:border-black transition-colors"
                            placeholder="Tuliskan klarifikasi, penjelasan, rencana perbaikan, atau solusi dari pihak institusi..."></textarea>
                        @error('adminResponse') <span class="text-xs text-rose-600 block">{{ $message }}</span> @enderror

                        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                            <div class="flex items-center gap-2 text-xs">
                                <label for="newStatus" class="font-bold text-slate-700">Perbarui Status Jadi:</label>
                                <select wire:model="newStatus" id="newStatus"
                                    class="px-3 py-1.5 bg-white border border-slate-300 rounded-full text-xs font-bold focus:ring-black">
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">Sedang Diproses</option>
                                    <option value="resolved">Selesai / Tuntas</option>
                                    <option value="rejected">Ditolak</option>
                                </select>
                            </div>

                            <button type="submit" wire:loading.attr="disabled"
                                class="px-6 py-2.5 bg-black hover:bg-slate-900 text-white font-bold rounded-full text-xs shadow-sm transition-all disabled:opacity-50">
                                <span wire:loading.remove>Kirim Tanggapan Resmi</span>
                                <span wire:loading>Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @endauth
    </div>
</div>
