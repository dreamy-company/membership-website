@php
    $segments = request()->segments();
    $breadcrumbs = [];
    $url = '';

    foreach ($segments as $segment) {
        $url .= '/' . $segment;
        $breadcrumbs[] = [
            'title' => \Illuminate\Support\Str::title(str_replace('-', ' ', $segment)),
            'url' => $url,
        ];
    }

    // item terakhir = current page, tidak clickable
    if(count($breadcrumbs) > 0){
        $breadcrumbs[count($breadcrumbs) - 1]['url'] = null;
    }
@endphp

<nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        @foreach ($breadcrumbs as $index => $item)
            <li class="inline-flex items-center">
                @if(isset($item['url']) && $item['url'])
                    <a href="{{ $item['url'] }}" class="text-gray-500 hover:text-gray-700">
                        {{ $item['title'] }}
                    </a>
                @else
                    <span class="text-gray-700 font-medium">{{ $item['title'] }}</span>
                @endif

                @if($index < count($breadcrumbs) - 1)
                    <svg class="w-4 h-4 mx-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
