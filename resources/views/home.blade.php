@php
    $content = \App\Models\PageContent::forPage('home')->data;
@endphp
<x-layouts.site>

    {{-- Hero: crossfading slides, pure CSS animation (no JS dependency) --}}
    <section class="relative h-[100svh] min-h-[600px] w-full overflow-hidden bg-[#07395b]">
        @php
            $slides = $content['hero_slides'] ?? [];
            $slideCount = max(count($slides), 1);
        @endphp

        <style>
            @keyframes heroFade {
                0%, 6% { opacity: 1; }
                {{ round(100 / $slideCount) - 4 }}%, {{ round(100 / $slideCount) + 4 }}% { opacity: 0; }
                {{ 100 - 2 }}%, 100% { opacity: 1; }
            }
            .hero-slide { animation: heroFade {{ $slideCount * 6 }}s ease-in-out infinite; }
        </style>

        @foreach ($slides as $i => $slide)
            <div
                class="hero-slide absolute inset-0 bg-cover bg-center"
                style="background-image: linear-gradient(180deg, rgba(7,57,91,.55), rgba(5,35,58,.85)), url('{{ \App\Models\PageContent::imageUrl($slide['image'] ?? null) }}'); animation-delay: {{ $i * 6 }}s;"
            ></div>
        @endforeach

        <div class="relative z-10 mx-auto flex h-full max-w-5xl flex-col items-center justify-center px-6 text-center">
            @if (!empty($content['hero_badge']))
                <span class="mb-5 inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-[#a9e17a] ring-1 ring-white/20 backdrop-blur">
                    {{ $content['hero_badge'] }}
                </span>
            @endif
            <h1 class="text-4xl font-extrabold leading-tight text-white sm:text-5xl lg:text-6xl" style="font-family: 'Poppins', sans-serif;">
                {{ $content['hero_title'] ?? '' }}
            </h1>
            @if (!empty($content['hero_text']))
                <p class="mt-6 max-w-2xl text-lg text-white/80 sm:text-xl">
                    {{ $content['hero_text'] }}
                </p>
            @endif
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a href="#about" class="rounded-full bg-[#7ec13d] px-8 py-3.5 text-sm font-bold text-[#07395b] shadow-xl shadow-[#7ec13d]/25 transition hover:-translate-y-0.5 hover:bg-[#8fd44f]">
                    Découvrir notre mission
                </a>
                <a href="{{ route('contact') }}" class="rounded-full border border-white/30 px-8 py-3.5 text-sm font-bold text-white transition hover:bg-white/10">
                    Contactez-nous
                </a>
            </div>
        </div>

        <div class="absolute inset-x-0 bottom-8 z-10 flex justify-center gap-2">
            @foreach ($slides as $slide)
                <span class="h-1.5 w-8 rounded-full bg-white/30"></span>
            @endforeach
        </div>
    </section>

    {{-- Mission / Vision / Valeur --}}
    <section class="relative z-10 mx-auto -mt-16 max-w-6xl px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 rounded-3xl bg-white p-2 shadow-2xl shadow-slate-900/10 ring-1 ring-slate-900/5 sm:grid-cols-3">
            @php
                $pillars = [
                    ['title' => $content['mission_title'] ?? 'Notre Mission', 'text' => $content['mission_text'] ?? '', 'icon' => 'M12 2.25a9.75 9.75 0 1 0 0 19.5 9.75 9.75 0 0 0 0-19.5Zm0 3a6.75 6.75 0 1 1 0 13.5 6.75 6.75 0 0 1 0-13.5Zm0 3a3.75 3.75 0 1 1 0 7.5 3.75 3.75 0 0 1 0-7.5Z'],
                    ['title' => $content['vision_title'] ?? 'Notre Vision', 'text' => $content['vision_text'] ?? '', 'icon' => 'M12 4.5c-5.5 0-9.75 4.72-9.75 7.5s4.25 7.5 9.75 7.5 9.75-4.72 9.75-7.5S17.5 4.5 12 4.5Zm0 11.25a3.75 3.75 0 1 1 0-7.5 3.75 3.75 0 0 1 0 7.5Z'],
                    ['title' => $content['value_title'] ?? 'Notre Valeur', 'text' => $content['value_text'] ?? '', 'icon' => 'M11.645 20.91a.75.75 0 0 0 .71 0c.201-.11 4.878-2.677 7.462-6.052 1.712-2.235 2.683-4.7 2.683-6.708C22.5 5.056 19.914 2.25 16.5 2.25c-1.62 0-3.13.79-4.5 2.27a6.55 6.55 0 0 0-4.5-2.27C4.086 2.25 1.5 5.056 1.5 8.15c0 2.008.971 4.473 2.683 6.708 2.584 3.375 7.261 5.942 7.462 6.052Z'],
                ];
            @endphp
            @foreach ($pillars as $pillar)
                <div class="group rounded-[1.4rem] p-8 text-center transition hover:bg-[#f6faf0] sm:text-left">
                    <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-[#eef7e4] text-[#4f8a10] transition group-hover:bg-[#7ec13d] group-hover:text-white">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-7 w-7"><path d="{{ $pillar['icon'] }}" /></svg>
                    </span>
                    <h3 class="mt-5 text-lg font-bold text-[#07395b]">{{ $pillar['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $pillar['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- About --}}
    <section id="about" class="mx-auto max-w-7xl scroll-mt-24 px-6 py-24 lg:px-8">
        <div class="grid grid-cols-1 items-center gap-14 lg:grid-cols-2">
            <div class="relative">
                <div class="absolute -inset-4 -z-10 rounded-[2rem] bg-[#eef7e4]"></div>
                <img src="{{ \App\Models\PageContent::imageUrl($content['about_image'] ?? null) }}" alt="Young Life Haïti" class="w-full rounded-2xl shadow-xl">
            </div>
            <div>
                @if (!empty($content['about_eyebrow']))
                    <span class="text-xs font-bold uppercase tracking-widest text-[#4f8a10]">{{ $content['about_eyebrow'] }}</span>
                @endif
                <h2 class="mt-3 text-3xl font-extrabold text-[#07395b] sm:text-4xl" style="font-family: 'Poppins', sans-serif;">
                    {{ $content['about_title'] ?? '' }}
                </h2>
                <ul class="mt-8 space-y-5">
                    @foreach ($content['about_checklist'] ?? [] as $point)
                        <li class="flex gap-3">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#7ec13d] text-white">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.4 7.4a1 1 0 0 1-1.4 0L3.3 9.5a1 1 0 1 1 1.4-1.4l3.9 3.9 6.7-6.7a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/></svg>
                            </span>
                            <p class="text-sm leading-relaxed text-slate-600">{{ $point['text'] ?? '' }}</p>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('a-propos') }}" class="mt-9 inline-flex items-center gap-2 rounded-full bg-[#07395b] px-7 py-3 text-sm font-bold text-white transition hover:bg-[#0a4a73]">
                    En savoir plus
                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 0 1 1.414 0l6 6a1 1 0 0 1 0 1.414l-6 6a1 1 0 0 1-1.414-1.414L14.586 11H3a1 1 0 1 1 0-2h11.586l-4.293-4.293a1 1 0 0 1 0-1.414Z" clip-rule="evenodd"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- JennVi call to action band --}}
    <section class="relative overflow-hidden bg-[#07395b] py-24">
        <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-[#7ec13d]/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-[#7ec13d]/10 blur-3xl"></div>
        <div class="relative mx-auto max-w-4xl px-6 text-center">
            <h2 class="text-6xl font-black uppercase tracking-tight text-[#7ec13d] sm:text-7xl" style="font-family: 'Poppins', sans-serif;">{{ $content['cta_title'] ?? '' }}</h2>
            <p class="mx-auto mt-6 max-w-2xl text-lg font-medium leading-relaxed text-white/90">
                {{ $content['cta_text'] ?? '' }}
            </p>
            <a href="{{ route('contact') }}" class="mt-9 inline-flex items-center rounded-full bg-white px-8 py-3.5 text-sm font-bold text-[#07395b] shadow-xl transition hover:-translate-y-0.5 hover:bg-[#f2f2f2]">
                Contactez-nous
            </a>
        </div>
    </section>

    {{-- Camps highlight --}}
    <section class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            @if (!empty($content['programs_eyebrow']))
                <span class="text-xs font-bold uppercase tracking-widest text-[#4f8a10]">{{ $content['programs_eyebrow'] }}</span>
            @endif
            <h2 class="mt-3 text-3xl font-extrabold text-[#07395b] sm:text-4xl" style="font-family: 'Poppins', sans-serif;">
                {{ $content['programs_title'] ?? '' }}
            </h2>
            <p class="mt-4 text-sm leading-relaxed text-slate-500">
                {{ $content['programs_text'] ?? '' }}
            </p>
        </div>

        <div class="mt-14 grid grid-cols-1 gap-8 md:grid-cols-3">
            @foreach ($content['camp_highlights'] ?? [] as $camp)
                <article class="group overflow-hidden rounded-2xl bg-white shadow-lg shadow-slate-900/5 ring-1 ring-slate-900/5 transition hover:-translate-y-1 hover:shadow-2xl">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="{{ \App\Models\PageContent::imageUrl($camp['image'] ?? null) }}" alt="{{ $camp['title'] ?? '' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-[#07395b]">{{ $camp['title'] ?? '' }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $camp['text'] ?? '' }}</p>
                        <a href="{{ route('gallery') }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-[#4f8a10] transition group-hover:gap-2.5">
                            Lire plus
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 0 1 1.414 0l6 6a1 1 0 0 1 0 1.414l-6 6a1 1 0 0 1-1.414-1.414L14.586 11H3a1 1 0 1 1 0-2h11.586l-4.293-4.293a1 1 0 0 1 0-1.414Z" clip-rule="evenodd"/></svg>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Testimonial / parallax quote --}}
    <section
        class="relative flex min-h-[420px] items-center justify-center bg-cover bg-fixed bg-center px-6 py-24 text-center"
        style="background-image: linear-gradient(180deg, rgba(5,35,58,.82), rgba(5,35,58,.82)), url('{{ \App\Models\PageContent::imageUrl($content['testimonial_image'] ?? null) }}');"
    >
        <p class="mx-auto max-w-3xl text-2xl font-semibold italic leading-relaxed text-white sm:text-3xl" style="font-family: 'Poppins', sans-serif;">
            &ldquo;{{ $content['testimonial_text'] ?? '' }}&rdquo;
        </p>
    </section>

    {{-- Gallery preview --}}
    <section id="gallery" class="mx-auto max-w-7xl scroll-mt-24 px-6 py-24 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-[#4f8a10]">En images</span>
            <h2 class="mt-3 text-3xl font-extrabold text-[#07395b] sm:text-4xl" style="font-family: 'Poppins', sans-serif;">
                Nos activités marquantes
            </h2>
            <p class="mt-4 text-sm leading-relaxed text-slate-500">
                Young Life est un ministère chrétien qui s'adresse aux collégiens, lycéens et étudiants dans les 50 États des États-Unis et dans plus de 100 pays à travers le monde.
            </p>
        </div>

        @php
            $galleryPreview = collect(\App\Models\PageContent::forPage('galerie')->data['photos'] ?? [])->take(8);
        @endphp
        <div class="mt-14 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($galleryPreview as $photo)
                <a href="{{ route('gallery') }}" class="group relative aspect-square overflow-hidden rounded-2xl bg-slate-100">
                    <img src="{{ \App\Models\PageContent::imageUrl($photo['image'] ?? null) }}" alt="Young Life Haïti" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 transition group-hover:opacity-100"></div>
                </a>
            @endforeach
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('gallery') }}" class="inline-flex items-center gap-2 rounded-full bg-[#07395b] px-7 py-3 text-sm font-bold text-white transition hover:bg-[#0a4a73]">
                Voir toute la galerie
                <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 0 1 1.414 0l6 6a1 1 0 0 1 0 1.414l-6 6a1 1 0 0 1-1.414-1.414L14.586 11H3a1 1 0 1 1 0-2h11.586l-4.293-4.293a1 1 0 0 1 0-1.414Z" clip-rule="evenodd"/></svg>
            </a>
        </div>

        @if (!empty($content['gallery_quote']))
            <p class="mt-10 text-center text-sm italic text-slate-500">
                &ldquo;{{ $content['gallery_quote'] }}&rdquo;
            </p>
        @endif
    </section>

</x-layouts.site>
