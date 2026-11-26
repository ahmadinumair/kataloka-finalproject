<div class="space-y-6 font-space">
    <!-- Top Header & Action Buttons -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-[#B5F938] text-black mb-2 uppercase">
                ⚙️ Meja Kerja Admin
            </span>
            <h1 class="font-syne text-3xl sm:text-4xl font-extrabold text-black tracking-tight">
                Kelola Aspirasi & Aduan Mahasiswa
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Verifikasi, ubah status pengerjaan mandiri atau massal, dan tindak lanjuti aspirasi mahasiswa.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- Tombol Cetak / Export Laporan -->
            <button type="button" onclick="window.print()"
                class="px-5 py-2.5 bg-white hover:bg-slate-50 text-black border border-slate-300 rounded-full text-xs font-bold shadow-2xs transition-colors flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak / Export Laporan
            </button>

            <a href="{{ route('admin.dashboard') }}" wire:navigate
                class="px-5 py-2.5 bg-black hover:bg-slate-900 text-white rounded-full text-xs font-bold transition-colors flex items-center gap-1.5">
                📊 Dashboard Grafik
            </a>
        </div>
    </div>

    <!-- Bulk Action Toolbar (Muncul Mengambang / Aktif Saat Ada Aspirasi yang Dipilih) -->
    @if(count($selectedFeedbacks) > 0)
        <div class="sticky top-24 z-30 bg-black text-white p-4 sm:p-5 rounded-[28px] shadow-xl border border-white/10 flex flex-wrap items-center justify-between gap-4 transition-all animate-in fade-in slide-in-from-top-4">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-[#B5F938] text-black font-extrabold flex items-center justify-center text-xs">
                    {{ count($selectedFeedbacks) }}
                </span>
                <div>
                    <h4 class="font-syne text-sm font-bold text-white">{{ count($selectedFeedbacks) }} Aspirasi Terpilih</h4>
                    <p class="text-[11px] text-slate-400">Pilih tindakan massal yang ingin diterapkan sekaligus:</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Tandai Diproses -->
                <button type="button" wire:click="bulkUpdateStatus('in_progress')"
                    class="px-4 py-2 bg-amber-400 hover:bg-amber-500 text-black font-bold rounded-full text-xs transition-colors flex items-center gap-1.5">
                    ⚙️ Tandai: Sedang Diproses
                </button>

                <!-- Tandai Tuntas / Selesai -->
                <button type="button" wire:click="bulkUpdateStatus('resolved')"
                    class="px-4 py-2 bg-[#B5F938] hover:bg-[#a1f316] text-black font-extrabold rounded-full text-xs transition-colors flex items-center gap-1.5 shadow-sm">
                    ✅ Tandai: Tuntas / Selesai
                </button>

                <!-- Kembalikan ke Pending -->
                <button type="button" wire:click="bulkUpdateStatus('pending')"
                    class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-full text-xs transition-colors">
                    ⏳ Set: Pending
                </button>

                <!-- Batalkan Pilihan -->
                <button type="button" wire:click="clearSelection"
                    class="px-3.5 py-2 text-slate-400 hover:text-white font-bold text-xs transition-colors">
                    ✕ Batal
                </button>
            </div>
        </div>
    @endif

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-5 rounded-[28px] border border-slate-200/80 shadow-xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <div class="lg:col-span-1 relative">
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Cari judul, pelapor, atau isi..."
                    class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:ring-2 focus:ring-black focus:border-black">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <div>
                <select wire:model.live="category"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:ring-black">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select wire:model.live="status"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:ring-black">
                    <option value="">Semua Status</option>
                    <option value="pending">⏳ Pending</option>
                    <option value="in_progress">⚙️ Diproses</option>
                    <option value="resolved">✅ Selesai</option>
                    <option value="rejected">❌ Ditolak</option>
                </select>
            </div>

            <div>
                <select wire:model.live="urgency"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:ring-black">
                    <option value="">Semua Urgensi AI</option>
                    <option value="urgent">🚨 Urgent</option>
                    <option value="high">⚠️ High</option>
                    <option value="normal">🔵 Normal</option>
                    <option value="low">🟢 Low</option>
                </select>
            </div>
        </div>

        @if(!empty($search) || !empty($category) || !empty($status) || !empty($urgency))
            <div class="flex justify-end pt-1">
                <button type="button" wire:click="resetFilters" class="text-xs text-rose-600 font-bold hover:underline">
                    ↺ Reset Filter
                </button>
            </div>
        @endif
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-[32px] border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#ECEEE8] border-b border-black/5 text-black uppercase tracking-wider text-[11px] font-bold">
                        <!-- Checkbox Select All -->
                        <th class="py-4 px-4 w-12 text-center">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="rounded border-slate-300 text-black focus:ring-black h-4 w-4 cursor-pointer"
                                title="Pilih Semua di Halaman Ini">
                        </th>
                        <th class="py-4 px-3 w-16">ID</th>
                        <th class="py-4 px-4">Aspirasi & Pengirim</th>
                        <th class="py-4 px-4">Kategori & Urgensi</th>
                        <th class="py-4 px-4">Respon</th>
                        <th class="py-4 px-4">Status & Ubah Cepat</th>
                        <th class="py-4 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($feedbacks as $fb)
                        <tr class="transition-colors {{ in_array((string)$fb->id, $selectedFeedbacks) ? 'bg-[#B5F938]/15 hover:bg-[#B5F938]/20' : 'hover:bg-slate-50' }}">
                            <!-- Checkbox Item -->
                            <td class="py-4 px-4 text-center">
                                <input type="checkbox" wire:model.live="selectedFeedbacks" value="{{ (string)$fb->id }}"
                                    class="rounded border-slate-300 text-black focus:ring-black h-4 w-4 cursor-pointer">
                            </td>

                            <td class="py-4 px-3 font-mono font-bold text-slate-400">#{{ $fb->id }}</td>

                            <!-- Judul & Pengirim -->
                            <td class="py-4 px-4 max-w-xs sm:max-w-md">
                                <a href="{{ route('feedbacks.detail', $fb->id) }}" wire:navigate class="font-syne font-bold text-black text-sm hover:underline block">
                                    {{ $fb->title }}
                                </a>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Oleh: <span class="text-black font-semibold">{{ $fb->is_anonymous ? '👤 Anonim' : $fb->user->name }}</span>
                                    • {{ $fb->created_at->format('d/m/Y H:i') }}
                                </p>
                                @if($fb->ai_summary)
                                    <p class="text-[11px] text-slate-700 italic line-clamp-1 mt-1 bg-[#F7F8F5] p-1.5 rounded-lg border border-black/5">
                                        🤖 AI: {{ $fb->ai_summary }}
                                    </p>
                                @endif
                            </td>

                            <!-- Kategori & Urgensi -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#ECEEE8] text-black block w-fit mb-1">
                                    {{ $fb->category->name }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold block w-fit
                                    {{ $fb->urgency === 'urgent' ? 'bg-black text-[#B5F938]' :
                                       ($fb->urgency === 'high' ? 'bg-amber-100 text-amber-900' :
                                       ($fb->urgency === 'low' ? 'bg-slate-100 text-slate-700' : 'bg-blue-100 text-blue-900')) }}">
                                    Urgensi: {{ ucfirst($fb->urgency) }}
                                </span>
                            </td>

                            <!-- Respon Count -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 font-bold text-xs {{ $fb->responses->count() > 0 ? 'text-black' : 'text-slate-400' }}">
                                    💬 {{ $fb->responses->count() }} Respon
                                </span>
                            </td>

                            <!-- Status Dropdown (Quick Livewire Action) -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <select wire:change="updateStatus({{ $fb->id }}, $event.target.value)"
                                    class="text-xs font-bold px-3 py-1.5 rounded-full border border-slate-200 transition-colors focus:ring-2 focus:ring-black
                                    {{ $fb->status === 'resolved' ? 'bg-[#B5F938] text-black font-extrabold' :
                                       ($fb->status === 'in_progress' ? 'bg-amber-100 text-amber-900' :
                                       ($fb->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700')) }}">
                                    <option value="pending" {{ $fb->status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                    <option value="in_progress" {{ $fb->status === 'in_progress' ? 'selected' : '' }}>⚙️ Diproses</option>
                                    <option value="resolved" {{ $fb->status === 'resolved' ? 'selected' : '' }}>✅ Selesai</option>
                                    <option value="rejected" {{ $fb->status === 'rejected' ? 'selected' : '' }}>❌ Ditolak</option>
                                </select>
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-4 px-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('feedbacks.detail', $fb->id) }}" wire:navigate
                                        class="px-3 py-1.5 bg-black hover:bg-slate-800 text-white rounded-full text-xs font-bold transition-colors">
                                        Rincian & Jawab
                                    </a>
                                    <button type="button" wire:click="deleteFeedback({{ $fb->id }})" wire:confirm="Apakah Anda yakin ingin menghapus aspirasi ini?"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 rounded-full transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400 text-xs">
                                Tidak ada data aspirasi yang sesuai kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $feedbacks->links() }}
        </div>
    </div>
</div>
