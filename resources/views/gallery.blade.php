@php
    $content = \App\Models\PageContent::forPage('galerie')->data;
@endphp
<x-layouts.site title="Galerie — Young Life Haïti" description="La galerie photo de Young Life Haïti : camps, clubs, formations et activités.">

    <section class="bg-[#07395b] pb-16 pt-32 text-center">
        @if (!empty($content['hero_eyebrow']))
            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-[#a9e17a] ring-1 ring-white/20">
                {{ $content['hero_eyebrow'] }}
            </span>
        @endif
        <h1 class="mt-4 text-4xl font-extrabold text-white sm:text-5xl" style="font-family: 'Poppins', sans-serif;">
            {{ $content['hero_title'] ?? '' }}
        </h1>
        @if (!empty($content['hero_text']))
            <p class="mx-auto mt-4 max-w-xl px-6 text-white/75">
                {{ $content['hero_text'] }}
            </p>
        @endif
    </section>

    <section class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($content['photos'] ?? [] as $photo)
                @php $src = \App\Models\PageContent::imageUrl($photo['image'] ?? null); @endphp
                <button
                    type="button"
                    onclick="openGalleryLightbox('{{ $src }}')"
                    class="group relative aspect-square overflow-hidden rounded-xl bg-slate-100"
                >
                    <img src="{{ $src }}" alt="Young Life Haïti" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 flex items-center justify-center bg-black/0 opacity-0 transition group-hover:bg-black/30 group-hover:opacity-100">
                        <span class="grid h-10 w-10 place-items-center rounded-full bg-white/90 text-[#07395b]">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.348 9.865l3.143 3.143a.75.75 0 1 0 1.06-1.06l-3.142-3.144A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/></svg>
                        </span>
                    </div>
                </button>
            @endforeach
        </div>
    </section>

    {{-- Shared lightbox: one dialog, JS swaps the image src on click. --}}
    <dialog id="gallery-lightbox" class="fixed left-1/2 top-1/2 m-0 w-[calc(100%-2rem)] max-w-4xl -translate-x-1/2 -translate-y-1/2 rounded-2xl bg-transparent p-0 backdrop:bg-slate-900/85">
        <div class="relative">
            <button
                type="button"
                onclick="document.getElementById('gallery-lightbox').close()"
                class="absolute -top-3 -right-3 grid h-9 w-9 place-items-center rounded-full bg-white text-slate-600 shadow-lg transition hover:text-slate-900"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
            </button>
            <img id="gallery-lightbox-img" src="" alt="Young Life Haïti" class="max-h-[85vh] w-full rounded-2xl object-contain">
        </div>
    </dialog>

    <script>
        function openGalleryLightbox(src) {
            document.getElementById('gallery-lightbox-img').src = src;
            document.getElementById('gallery-lightbox').showModal();
        }
    </script>

</x-layouts.site>
