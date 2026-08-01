<x-layouts.site title="Contact — Young Life Haïti" description="Contactez Young Life Haïti : #10, Delmas 26, Port-au-Prince — (+509) 22 27 9026 — smkdyounglifehaiti@gmail.com">

    <section class="bg-[#07395b] pb-16 pt-32 text-center">
        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-[#a9e17a] ring-1 ring-white/20">
            Nous écrire
        </span>
        <h1 class="mt-4 text-4xl font-extrabold text-white sm:text-5xl" style="font-family: 'Poppins', sans-serif;">
            Contactez-nous
        </h1>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-[#4f8a10]">Services de la mission</span>
            <h2 class="mt-3 text-3xl font-extrabold text-[#07395b] sm:text-4xl" style="font-family: 'Poppins', sans-serif;">
                Young Life Mission Services
            </h2>
            <p class="mt-4 text-sm leading-relaxed text-slate-500">
                Si vous avez des questions d'ordre général sur Young Life ou si vous ne savez pas trop qui contacter, veuillez contacter Young Life Mission Services.
            </p>
        </div>

        <div class="mt-16 grid grid-cols-1 gap-12 lg:grid-cols-5">
            {{-- Contact details --}}
            <div class="lg:col-span-2">
                <h3 class="text-lg font-bold text-[#07395b]">Coordonnées</h3>

                <ul class="mt-6 space-y-5">
                    @php
                        $details = [
                            ['icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75', 'text' => '#10, Delmas 26, Port-au-Prince, Haïti'],
                            ['icon' => 'M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z', 'text' => '(+509) 22 27 9026', 'href' => 'tel:+50922279026'],
                            ['icon' => 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75', 'text' => 'smkdyounglifehaiti@gmail.com', 'href' => 'mailto:smkdyounglifehaiti@gmail.com'],
                        ];
                    @endphp
                    @foreach ($details as $detail)
                        <li class="flex items-start gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#eef7e4] text-[#4f8a10]">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $detail['icon'] }}" /></svg>
                            </span>
                            @if (isset($detail['href']))
                                <a href="{{ $detail['href'] }}" class="mt-2.5 text-sm font-semibold text-slate-700 transition hover:text-[#4f8a10]">{{ $detail['text'] }}</a>
                            @else
                                <span class="mt-2.5 text-sm font-semibold text-slate-700">{{ $detail['text'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <div class="mt-8 flex gap-3">
                    @php
                        $socials = [
                            ['label' => 'Facebook', 'href' => 'https://www.facebook.com/younglifehaitijennvi/', 'path' => 'M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5 3.66 9.16 8.44 9.94v-7.03H7.9v-2.9h2.54V9.85c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.44 2.9h-2.34V22c4.78-.78 8.44-4.94 8.44-9.94Z'],
                            ['label' => 'Twitter', 'href' => 'https://twitter.com/YJennvi', 'path' => 'M22 5.9c-.7.3-1.5.5-2.3.7.8-.5 1.5-1.3 1.8-2.3-.8.5-1.7.8-2.6 1a4.1 4.1 0 0 0-7 3.8A11.6 11.6 0 0 1 3.4 4.6a4.2 4.2 0 0 0 1.3 5.5c-.6 0-1.3-.2-1.8-.5v.1c0 2 1.4 3.6 3.3 4a4.1 4.1 0 0 1-1.8.1 4.1 4.1 0 0 0 3.9 2.9A8.3 8.3 0 0 1 2 18.4a11.6 11.6 0 0 0 6.3 1.8c7.5 0 11.7-6.4 11.7-11.9v-.5c.8-.6 1.5-1.3 2-2.1Z'],
                            ['label' => 'Instagram', 'href' => 'https://www.instagram.com/younglifehaiti_jennvi/', 'path' => 'M12 2.2c2.7 0 3 0 4 .1 1 .1 1.6.2 2 .4.5.2.9.4 1.3.8.4.4.6.8.8 1.3.2.4.3 1 .4 2 .1 1 .1 1.3.1 4s0 3-.1 4c-.1 1-.2 1.6-.4 2-.2.5-.4.9-.8 1.3-.4.4-.8.6-1.3.8-.4.2-1 .3-2 .4-1 .1-1.3.1-4 .1s-3 0-4-.1c-1-.1-1.6-.2-2-.4a3.5 3.5 0 0 1-1.3-.8 3.5 3.5 0 0 1-.8-1.3c-.2-.4-.3-1-.4-2-.1-1-.1-1.3-.1-4s0-3 .1-4c.1-1 .2-1.6.4-2 .2-.5.4-.9.8-1.3.4-.4.8-.6 1.3-.8.4-.2 1-.3 2-.4 1-.1 1.3-.1 4-.1Zm0 1.8c-2.6 0-2.9 0-4 .1-.8 0-1.3.2-1.6.3-.4.1-.7.3-1 .6-.3.3-.5.6-.6 1-.1.3-.3.8-.3 1.6-.1 1.1-.1 1.4-.1 4s0 2.9.1 4c0 .8.2 1.3.3 1.6.1.4.3.7.6 1 .3.3.6.5 1 .6.3.1.8.3 1.6.3 1.1.1 1.4.1 4 .1s2.9 0 4-.1c.8 0 1.3-.2 1.6-.3.4-.1.7-.3 1-.6.3-.3.5-.6.6-1 .1-.3.3-.8.3-1.6.1-1.1.1-1.4.1-4s0-2.9-.1-4c0-.8-.2-1.3-.3-1.6a2.7 2.7 0 0 0-.6-1 2.7 2.7 0 0 0-1-.6c-.3-.1-.8-.3-1.6-.3-1.1-.1-1.4-.1-4-.1Zm0 3a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.8a3.2 3.2 0 1 0 0 6.4 3.2 3.2 0 0 0 0-6.4Zm5.2-2a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4Z'],
                        ];
                    @endphp
                    @foreach ($socials as $s)
                        <a href="{{ $s['href'] }}" aria-label="{{ $s['label'] }}" class="grid h-10 w-10 place-items-center rounded-full bg-[#f6faf0] text-[#07395b] transition hover:bg-[#7ec13d] hover:text-white">
                            <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="{{ $s['path'] }}" /></svg>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Contact form --}}
            <div class="lg:col-span-3">
                @if (session('contact_sent'))
                    <div class="mb-6 flex items-start gap-3 rounded-xl bg-[#f6faf0] p-4 text-sm font-medium text-[#4f8a10]">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 h-5 w-5 shrink-0"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
                        Merci ! Votre message a bien été envoyé, nous vous répondrons rapidement.
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.send') }}" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Nom complet" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-[#7ec13d] focus:outline-none focus:ring-2 focus:ring-[#7ec13d]/30">
                            @error('name') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-[#7ec13d] focus:outline-none focus:ring-2 focus:ring-[#7ec13d]/30">
                            @error('email') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Sujet" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-[#7ec13d] focus:outline-none focus:ring-2 focus:ring-[#7ec13d]/30">
                    </div>
                    <div>
                        <textarea name="message" rows="6" placeholder="Message" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-[#7ec13d] focus:outline-none focus:ring-2 focus:ring-[#7ec13d]/30">{{ old('message') }}</textarea>
                        @error('message') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="rounded-full bg-[#07395b] px-8 py-3.5 text-sm font-bold text-white transition hover:bg-[#0a4a73]">
                        Envoyer
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- Map --}}
    <section class="h-[420px] w-full">
        <iframe
            src="https://www.google.com/maps?q={{ urlencode('#10, Delmas 26, Port-au-Prince, Haïti') }}&output=embed"
            class="h-full w-full border-0"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
    </section>

</x-layouts.site>
