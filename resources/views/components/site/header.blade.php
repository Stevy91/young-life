@php
    $nav = [
        ['label' => 'Accueil', 'href' => '/'],
        ['label' => 'Lève Fonds', 'href' => route('leve-fonds')],
        ['label' => 'Galerie', 'href' => route('gallery')],
        ['label' => 'À Propos', 'href' => route('a-propos')],
    ];
@endphp

<input type="checkbox" id="site-nav-toggle" class="peer hidden">

<header class="fixed inset-x-0 top-0 z-50 bg-[#07395b]/90 backdrop-blur-md shadow-[0_1px_0_0_rgba(255,255,255,0.08)]">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-6 lg:px-8">
        <a href="/" class="flex items-center gap-2 shrink-0">
            <img src="{{ asset('images/about/logo2.jpg') }}" alt="Young Life Haïti" class="h-10 w-auto rounded">
        </a>

        <nav class="hidden lg:flex lg:items-center lg:gap-1">
            @foreach ($nav as $item)
                <div class="group relative">
                    <a
                        href="{{ $item['href'] }}"
                        class="flex items-center gap-1 rounded-full px-4 py-2 text-sm font-semibold text-white/90 transition hover:bg-white/10 hover:text-white"
                    >
                        {{ $item['label'] }}
                        @if (!empty($item['children']))
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 opacity-70">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </a>

                    @if (!empty($item['children']))
                        <div class="invisible absolute left-1/2 top-full z-10 w-52 -translate-x-1/2 translate-y-1 rounded-xl bg-white p-2 opacity-0 shadow-xl ring-1 ring-black/5 transition duration-150 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
                            @foreach ($item['children'] as $child)
                                <a href="{{ $child['href'] }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-[#f3f8ee] hover:text-[#4f8a10]">
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </nav>

        <div class="hidden lg:flex lg:items-center lg:gap-3">
            <a href="{{ url('/admin/login') }}" class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-semibold text-white/80 transition hover:bg-white/10 hover:text-white">
                <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M6 10a.75.75 0 0 1 .75-.75h9.546l-1.048-.943a.75.75 0 1 1 1.004-1.114l2.5 2.25a.75.75 0 0 1 0 1.114l-2.5 2.25a.75.75 0 1 1-1.004-1.114l1.048-.943H6.75A.75.75 0 0 1 6 10Z" clip-rule="evenodd"/></svg>
                Se connecter
            </a>
            <a href="{{ route('contact') }}" class="inline-flex items-center rounded-full bg-[#7ec13d] px-5 py-2.5 text-sm font-bold text-[#07395b] shadow-lg shadow-[#7ec13d]/20 transition hover:bg-[#8fd44f]">
                Contactez-nous
            </a>
        </div>

        <label for="site-nav-toggle" class="grid h-10 w-10 cursor-pointer place-items-center rounded-full text-white transition hover:bg-white/10 lg:hidden">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-6 w-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
            </svg>
        </label>
    </div>

    <div class="hidden max-h-0 overflow-hidden border-t border-white/10 bg-[#07395b] transition-all duration-300 peer-checked:block peer-checked:max-h-[32rem] lg:hidden">
        <nav class="mx-auto max-w-7xl space-y-1 px-6 py-4">
            @foreach ($nav as $item)
                <a href="{{ $item['href'] }}" class="block rounded-lg px-3 py-2 text-base font-semibold text-white/90 hover:bg-white/10">
                    {{ $item['label'] }}
                </a>
                @if (!empty($item['children']))
                    <div class="ml-3 space-y-1 border-l border-white/10 pl-3">
                        @foreach ($item['children'] as $child)
                            <a href="{{ $child['href'] }}" class="block rounded-lg px-3 py-1.5 text-sm text-white/70 hover:bg-white/10 hover:text-white">
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            @endforeach
            <a href="{{ url('/admin/login') }}" class="block rounded-lg px-3 py-2 text-base font-semibold text-white/70 hover:bg-white/10 hover:text-white">
                Se connecter
            </a>
        </nav>
    </div>
</header>
