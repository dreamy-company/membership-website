@php
    // 1. Ambil path default dari request saat ini
    $path = request()->path();

    // 2. DETEKSI REQUEST LIVEWIRE
    // Jika path dimulai dengan 'livewire/', berarti ini adalah request AJAX internal Livewire
    // (misalnya saat klik tombol, validasi input, atau buka modal)
    if (request()->is('livewire/*')) {
        // Kita ambil URL asli yang sedang dilihat user dari header 'Referer'
        $referer = request()->header('Referer');
        
        // Ambil path-nya saja (buang domain http://localhost:8000...)
        $parsedPath = parse_url($referer, PHP_URL_PATH);
        
        // Bersihkan slash di awal agar konsisten
        $path = ltrim($parsedPath, '/');
    }

    // 3. Pecah path menjadi segments array
    // array_filter berguna untuk menghapus elemen kosong jika ada double slash
    $segments = array_values(array_filter(explode('/', $path)));

    $breadcrumbs = [];
    $url = '';

    // 4. Build array breadcrumbs
    foreach ($segments as $segment) {
        $url .= '/' . $segment;
        $breadcrumbs[] = [
            'title' => \Illuminate\Support\Str::title(str_replace('-', ' ', $segment)),
            'url' => $url,
        ];
    }

    // 5. Matikan link untuk item terakhir (halaman saat ini)
    if(count($breadcrumbs) > 0){
        $breadcrumbs[count($breadcrumbs) - 1]['url'] = null;
    }
@endphp

<nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        {{-- Link Home (Opsional, uncomment jika ingin selalu ada Home icon di depan) --}}
        {{-- 
        <li class="inline-flex items-center">
            <a href="{{ url('/') }}" class="inline-flex items-center text-gray-700 hover:text-gray-900">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                </svg>
                Home
            </a>
        </li> 
        --}}

        @foreach ($breadcrumbs as $index => $item)
            <li class="inline-flex items-center">
                {{-- Separator Icon (hanya muncul jika bukan item pertama, atau jika ada Home sebelumnya) --}}
                @if($index > 0)
                    <svg class="w-4 h-4 mx-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                @endif

                @if(isset($item['url']) && $item['url'])
                    {{-- Link Aktif --}}
                    <a href="{{ $item['url'] }}" class="text-gray-500 hover:text-gray-700 {{ $index > 0 ? 'ml-1 md:ml-2' : '' }}">
                        {{ $item['title'] }}
                    </a>
                @else
                    {{-- Current Page (Teks Tebal/Hitam) --}}
                    <span class="text-gray-700 font-medium {{ $index > 0 ? 'ml-1 md:ml-2' : '' }}">
                        {{ $item['title'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>