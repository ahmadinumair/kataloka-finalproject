@php
    if (! isset($scrollTo)) {
        $scrollTo = 'body';
    }

    $scrollIntoViewJsSnippet = ($scrollTo !== false)
        ? <<<JS
           (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView({ behavior: 'smooth' });
        JS
        : '';
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col sm:flex-row items-center justify-between gap-4 py-3 font-space">
        <!-- Informasi Jumlah Data -->
        <div class="text-xs text-slate-500 font-medium order-2 sm:order-1">
            Menampilkan
            <span class="font-bold text-black">{{ $paginator->firstItem() }}</span>
            sampai
            <span class="font-bold text-black">{{ $paginator->lastItem() }}</span>
            dari total
            <span class="font-bold text-black">{{ $paginator->total() }}</span>
            aspirasi
        </div>

        <!-- Tombol Nomor Halaman ke Samping (Moonli Style) -->
        <div class="inline-flex items-center gap-1 p-1.5 bg-white border border-slate-200/90 rounded-full shadow-2xs order-1 sm:order-2">
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-slate-300 cursor-not-allowed rounded-full">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </span>
            @else
                <button type="button"
                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-slate-700 hover:text-black hover:bg-[#ECEEE8] rounded-full transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </button>
            @endif

            {{-- Nomor-Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Separator Titik Tiga "..." --}}
                @if (is_string($element))
                    <span class="px-2 py-1 text-xs text-slate-400 font-bold select-none">{{ $element }}</span>
                @endif

                {{-- Array Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                            @if ($page == $paginator->currentPage())
                                <span class="inline-flex items-center justify-center min-w-[32px] h-8 px-2.5 text-xs font-syne font-extrabold bg-black text-[#B5F938] rounded-full shadow-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <button type="button"
                                    wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    class="inline-flex items-center justify-center min-w-[32px] h-8 px-2.5 text-xs font-syne font-bold text-slate-700 hover:text-black hover:bg-[#B5F938] rounded-full transition-all">
                                    {{ $page }}
                                </button>
                            @endif
                        </span>
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Selanjutnya --}}
            @if ($paginator->hasMorePages())
                <button type="button"
                    wire:click="nextPage('{{ $paginator->getPageName() }}')"
                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-slate-700 hover:text-black hover:bg-[#ECEEE8] rounded-full transition-colors">
                    <span class="hidden sm:inline">Selanjutnya</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </button>
            @else
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-slate-300 cursor-not-allowed rounded-full">
                    <span class="hidden sm:inline">Selanjutnya</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
