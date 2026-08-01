@php
    $content = \App\Models\PageContent::forPage('a-propos')->data;
@endphp
<x-layouts.site title="À Propos — Young Life Haïti" description="À propos de Young Life Haïti (Jennvi) : notre organisation et notre équipe.">

    {{-- Page header --}}
    <section class="bg-[#07395b] pb-16 pt-32 text-center">
        @if (!empty($content['hero_eyebrow']))
            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-[#a9e17a] ring-1 ring-white/20">
                {{ $content['hero_eyebrow'] }}
            </span>
        @endif
        <h1 class="mt-4 text-4xl font-extrabold text-white sm:text-5xl" style="font-family: 'Poppins', sans-serif;">
            {{ $content['hero_title'] ?? '' }}
        </h1>
    </section>

    {{-- About --}}
    <section class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            @if (!empty($content['about_eyebrow']))
                <span class="text-xs font-bold uppercase tracking-widest text-[#4f8a10]">{{ $content['about_eyebrow'] }}</span>
            @endif
            <h2 class="mt-3 text-3xl font-extrabold text-[#07395b] sm:text-4xl" style="font-family: 'Poppins', sans-serif;">
                {{ $content['about_title'] ?? '' }}
            </h2>
            @if (!empty($content['about_text']))
                <p class="mt-4 text-sm leading-relaxed text-slate-500">
                    {{ $content['about_text'] }}
                </p>
            @endif
        </div>

        <div class="mt-16 grid grid-cols-1 items-center gap-14 lg:grid-cols-2">
            <div class="relative">
                <div class="absolute -inset-4 -z-10 rounded-[2rem] bg-[#eef7e4]"></div>
                <img src="{{ \App\Models\PageContent::imageUrl($content['about_image'] ?? null) }}" alt="Young Life Haïti" class="w-full rounded-2xl shadow-xl">
            </div>
            <div>
                @if (!empty($content['about_paragraph']))
                    <p class="text-sm leading-relaxed text-slate-600">
                        {{ $content['about_paragraph'] }}
                    </p>
                @endif
                @if (!empty($content['quote_text']))
                    <blockquote class="mt-6 border-l-4 border-[#7ec13d] pl-5 text-lg font-semibold italic leading-relaxed text-[#07395b]">
                        &ldquo;{{ $content['quote_text'] }}&rdquo;
                    </blockquote>
                @endif

                <ul class="mt-8 grid grid-cols-2 gap-4">
                    @foreach ($content['values'] ?? [] as $value)
                        <li class="flex items-center gap-2.5 rounded-xl bg-[#f6faf0] px-4 py-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#7ec13d] text-white">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.4 7.4a1 1 0 0 1-1.4 0L3.3 9.5a1 1 0 1 1 1.4-1.4l3.9 3.9 6.7-6.7a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/></svg>
                            </span>
                            <span class="text-sm font-bold text-[#07395b]">{{ $value['text'] ?? '' }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- Team --}}
    <section class="bg-[#f8f7f2] py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                @if (!empty($content['team_eyebrow']))
                    <span class="text-xs font-bold uppercase tracking-widest text-[#4f8a10]">{{ $content['team_eyebrow'] }}</span>
                @endif
                <h2 class="mt-3 text-3xl font-extrabold text-[#07395b] sm:text-4xl" style="font-family: 'Poppins', sans-serif;">
                    {{ $content['team_title'] ?? '' }}
                </h2>
            </div>

            <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($content['team_members'] ?? [] as $member)
                    <div class="group overflow-hidden rounded-2xl bg-white text-center shadow-lg shadow-slate-900/5 ring-1 ring-slate-900/5">
                        <div class="aspect-square overflow-hidden bg-[#eef7e4]">
                            @if (!empty($member['photo']))
                                <img src="{{ \App\Models\PageContent::imageUrl($member['photo']) }}" alt="{{ $member['name'] ?? '' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-5xl font-extrabold text-[#7ec13d]" style="font-family: 'Poppins', sans-serif;">
                                    {{ collect(explode(' ', $member['name'] ?? ''))->map(fn ($w) => mb_substr($w, 0, 1))->implode('') }}
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-[#07395b]">{{ $member['name'] ?? '' }}</h3>
                            <p class="mt-1 text-sm font-medium text-[#4f8a10]">{{ $member['role'] ?? '' }}</p>
                            @if (!empty($member['facebook']))
                                <a href="{{ $member['facebook'] }}" aria-label="Facebook de {{ $member['name'] ?? '' }}" class="mt-4 inline-flex h-9 w-9 items-center justify-center rounded-full bg-[#f6faf0] text-[#07395b] transition hover:bg-[#7ec13d] hover:text-white">
                                    <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5 3.66 9.16 8.44 9.94v-7.03H7.9v-2.9h2.54V9.85c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.44 2.9h-2.34V22c4.78-.78 8.44-4.94 8.44-9.94Z"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</x-layouts.site>
