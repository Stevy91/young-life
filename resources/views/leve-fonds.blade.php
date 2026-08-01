@php
    $content = \App\Models\PageContent::forPage('leve-fonds')->data;
@endphp
<x-layouts.site title="Lève Fonds — Young Life Haïti" description="Semaine Lève Fonds Young Life Haïti : soutenez un bloc régional ou la direction nationale par MonCash ou NatCash.">

    {{-- Header banner: the image is a designed graphic with its own
    text/dates baked in, so it's shown in full (not cropped as a CSS
    background) rather than fighting it with an overlay. --}}
    @if (!empty($content['banner_image']))
        <section class="bg-[#07395b] pb-8 pt-28">
            <div class="mx-auto max-w-5xl px-6 lg:px-8">
                <img src="{{ \App\Models\PageContent::imageUrl($content['banner_image']) }}" alt="Semèn Lève Fon" class="w-full rounded-2xl shadow-2xl">
            </div>
        </section>
    @endif

    <section class="mx-auto max-w-2xl px-6 {{ empty($content['banner_image']) ? 'pt-32' : 'pt-14' }} text-center lg:px-8">
        @if (!empty($content['eyebrow']))
            <span class="text-xs font-bold uppercase tracking-widest text-[#4f8a10]">{{ $content['eyebrow'] }}</span>
        @endif
        <h1 class="mt-3 text-3xl font-extrabold text-[#07395b] sm:text-4xl" style="font-family: 'Poppins', sans-serif;">
            {{ $content['title'] ?? '' }}
        </h1>
        @if (!empty($content['intro_text']))
            <p class="mt-3 text-slate-500">
                {{ $content['intro_text'] }}
            </p>
        @endif
    </section>

    {{-- Achievements --}}
    <section class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            @if (!empty($content['stats_eyebrow']))
                <span class="text-xs font-bold uppercase tracking-widest text-[#4f8a10]">{{ $content['stats_eyebrow'] }}</span>
            @endif
            <h2 class="mt-3 text-3xl font-extrabold text-[#07395b] sm:text-4xl" style="font-family: 'Poppins', sans-serif;">
                {{ $content['stats_title'] ?? '' }}
            </h2>
        </div>

        @php
            $statIcons = [
                'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5',
                'M12 21c-4.97-3.34-9-7.24-9-11.4C3 6.6 5.4 4.5 8.1 4.5c1.7 0 3.15.9 3.9 2.25.75-1.35 2.2-2.25 3.9-2.25 2.7 0 5.1 2.1 5.1 5.1 0 4.16-4.03 8.06-9 11.4Z',
                'M12 2.25c-4 3-6.5 6.7-6.5 10.5a6.5 6.5 0 1 0 13 0c0-3.8-2.5-7.5-6.5-10.5Z',
                'M15 19.5c3.7 0 6.75-.5 6.75-2.9s-3-4.35-6.75-4.35S8.25 14.2 8.25 16.6s3.05 2.9 6.75 2.9ZM15 9.75a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5ZM4.5 19.5c-1.5 0-2.25-.4-2.25-1.9 0-2 1.9-3.6 4.25-3.6.5 0 1 .07 1.45.2M6.75 9.75a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z',
            ];
        @endphp
        <div class="mt-14 grid grid-cols-2 gap-6 lg:grid-cols-4">
            @foreach ($content['stats'] ?? [] as $i => $stat)
                <div class="rounded-2xl bg-[#f6faf0] p-6 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-[#7ec13d] text-white">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $statIcons[$i % count($statIcons)] }}" /></svg>
                    </span>
                    <p class="mt-4 text-3xl font-extrabold text-[#07395b]">{{ $stat['value'] ?? '' }}</p>
                    <p class="mt-1 text-sm font-medium text-slate-500">{{ $stat['label'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Donation blocks --}}
    <section class="bg-[#f8f7f2] py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                @if (!empty($content['blocks_eyebrow']))
                    <span class="text-xs font-bold uppercase tracking-widest text-[#4f8a10]">{{ $content['blocks_eyebrow'] }}</span>
                @endif
                <h2 class="mt-3 text-3xl font-extrabold text-[#07395b] sm:text-4xl" style="font-family: 'Poppins', sans-serif;">
                    {{ $content['blocks_title'] ?? '' }}
                </h2>
                @if (!empty($content['blocks_text']))
                    <p class="mt-4 text-sm leading-relaxed text-slate-500">
                        {{ $content['blocks_text'] }}
                    </p>
                @endif
            </div>

            <div class="mt-14 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($content['donation_blocks'] ?? [] as $i => $block)
                    @php
                        $zones = array_values(array_filter(preg_split('/\r?\n/', $block['awondisman'] ?? '')));
                        $progress = (float) ($block['progress'] ?? 0);
                    @endphp
                    <div class="flex flex-col rounded-2xl bg-white p-7 shadow-lg shadow-slate-900/5 ring-1 ring-slate-900/5">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#eef7e4] text-[#4f8a10]">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 6-7.5 10.5-7.5 10.5s-7.5-4.5-7.5-10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </span>
                        <h3 class="mt-5 text-lg font-bold text-[#07395b]">{{ $block['name'] ?? '' }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">
                            Rele nou oswa fè depo an pa MonCash, nan {{ $block['name'] ?? '' }} wap jwenn tout zòn ki nan Awondisman {{ $zones[0] ?? '' }} ak plis ankò.
                        </p>

                        <div class="mt-5">
                            <div class="flex items-center justify-between text-xs font-bold text-[#07395b]">
                                <span>OBJEKTIF</span>
                                <span>{{ rtrim(rtrim(number_format($progress, 2), '0'), '.') }}%</span>
                            </div>
                            <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-[#7ec13d]" style="width: {{ min($progress, 100) }}%"></div>
                            </div>
                        </div>

                        <button
                            type="button"
                            onclick="document.getElementById('modal-block-{{ $i }}').showModal()"
                            class="mt-6 inline-flex items-center justify-center gap-2 rounded-full bg-[#07395b] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#0a4a73]"
                        >
                            <img src="{{ \App\Models\PageContent::imageUrl('page-content/payments/moncash.png') }}" alt="" class="h-4 w-4 rounded-sm">
                            Fè yon don
                        </button>
                    </div>

                    {{-- Tailwind's Preflight zeroes out margin globally, which breaks the
                    native dialog:modal { margin: auto } centering trick — position it
                    explicitly instead of relying on the browser default. --}}
                    <dialog id="modal-block-{{ $i }}" class="fixed left-1/2 top-1/2 m-0 w-[calc(100%-2rem)] max-w-md -translate-x-1/2 -translate-y-1/2 rounded-2xl p-0 backdrop:bg-slate-900/60">
                        <div class="p-7">
                            <div class="flex items-start justify-between">
                                <h3 class="text-lg font-bold text-[#07395b]">{{ $block['name'] ?? '' }}</h3>
                                <button type="button" onclick="document.getElementById('modal-block-{{ $i }}').close()" class="grid h-8 w-8 place-items-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                                </button>
                            </div>

                            <p class="mt-4 text-xs font-bold uppercase tracking-wider text-slate-400">Zòn ki kouvri</p>
                            <ul class="mt-2 grid grid-cols-2 gap-x-3 gap-y-1 text-sm text-slate-600">
                                @foreach ($zones as $zone)
                                    <li>&bull; {{ $zone }}</li>
                                @endforeach
                            </ul>

                            <div class="mt-6 space-y-3">
                                <div class="flex items-center gap-3 rounded-xl bg-[#f6faf0] p-3">
                                    <img src="{{ \App\Models\PageContent::imageUrl('page-content/payments/moncash.png') }}" alt="MonCash" class="h-8 w-8 rounded">
                                    <div>
                                        <p class="text-xs font-bold uppercase text-slate-400">MonCash</p>
                                        <p class="text-sm font-bold text-[#07395b]">{{ $block['moncash'] ?? '' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 rounded-xl bg-[#f6faf0] p-3">
                                    <img src="{{ \App\Models\PageContent::imageUrl('page-content/payments/natcash.png') }}" alt="NatCash" class="h-8 w-8 rounded">
                                    <div>
                                        <p class="text-xs font-bold uppercase text-slate-400">NatCash</p>
                                        <p class="text-sm font-bold text-[#07395b]">{{ $block['natcash'] ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </dialog>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Regional map gallery --}}
    <section class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            @if (!empty($content['regions_eyebrow']))
                <span class="text-xs font-bold uppercase tracking-widest text-[#4f8a10]">{{ $content['regions_eyebrow'] }}</span>
            @endif
            <h2 class="mt-3 text-3xl font-extrabold text-[#07395b] sm:text-4xl" style="font-family: 'Poppins', sans-serif;">
                {{ $content['regions_title'] ?? '' }}
            </h2>
        </div>

        <div class="mt-12 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($content['region_maps'] ?? [] as $i => $region)
                <button
                    type="button"
                    onclick="document.getElementById('lightbox-region-{{ $i }}').showModal()"
                    class="group relative aspect-[4/3] overflow-hidden rounded-xl bg-slate-100 text-left"
                >
                    <img src="{{ \App\Models\PageContent::imageUrl($region['image'] ?? null) }}" alt="Rejyon {{ $region['label'] ?? '' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                        <p class="text-xs font-bold uppercase tracking-wide text-white">{{ $region['label'] ?? '' }}</p>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center bg-black/0 opacity-0 transition group-hover:bg-black/20 group-hover:opacity-100">
                        <span class="grid h-10 w-10 place-items-center rounded-full bg-white/90 text-[#07395b]">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.348 9.865l3.143 3.143a.75.75 0 1 0 1.06-1.06l-3.142-3.144A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/></svg>
                        </span>
                    </div>
                </button>

                <dialog id="lightbox-region-{{ $i }}" class="fixed left-1/2 top-1/2 m-0 w-[calc(100%-2rem)] max-w-3xl -translate-x-1/2 -translate-y-1/2 rounded-2xl bg-transparent p-0 backdrop:bg-slate-900/80">
                    <div class="relative">
                        <button
                            type="button"
                            onclick="document.getElementById('lightbox-region-{{ $i }}').close()"
                            class="absolute -top-3 -right-3 grid h-9 w-9 place-items-center rounded-full bg-white text-slate-600 shadow-lg transition hover:text-slate-900"
                        >
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                        </button>
                        <img src="{{ \App\Models\PageContent::imageUrl($region['image'] ?? null) }}" alt="Rejyon {{ $region['label'] ?? '' }}" class="max-h-[80vh] w-full rounded-2xl object-contain">
                    </div>
                </dialog>
            @endforeach
        </div>
    </section>

</x-layouts.site>
