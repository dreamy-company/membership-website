@props(['title', 'total', 'route'])

<div class="bg-transparent block max-w-sm p-6 rounded-md shadow-sm">
    <div class="flex flex-row justify-between items-center">
        <h5 class="mb-3 text-xl font-semibold tracking-tight text-heading leading-8">{{ $title }}</h5>
        <h5 class="mb-3 text-2xl font-semibold tracking-tight text-heading leading-8">{{ $total }}</h5>
    </div>
    <a href="{{ route($route) }}" class="inline-flex items-center text-white bg-stone-900 box-border border border-transparent hover:bg-stone-800 focus:ring-4 focus:ring-stone-700 shadow-xs font-medium leading-5 rounded-md text-sm px-4 py-2.5 focus:outline-none">
        View Details
        <svg class="w-4 h-4 ms-1.5 rtl:rotate-180 -me-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4"/></svg>
    </a>
</div>
