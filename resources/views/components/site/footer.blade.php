<footer class="bg-[#052a41] text-white/80">
    <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <img src="{{ asset('images/about/logo.jpg') }}" alt="Young Life Haïti" class="h-14 w-auto rounded bg-white/5 p-1">
                <p class="mt-5 max-w-sm text-sm leading-relaxed text-white/60">
                    Young Life est un ministère chrétien qui s'adresse aux collégiens, lycéens et étudiants dans les 50 États des États-Unis et dans plus de 100 pays à travers le monde.
                </p>
                <div class="mt-6 flex gap-3">
                    @php
                        $socials = [
                            ['label' => 'Facebook', 'href' => 'https://www.facebook.com/younglifehaitijennvi/', 'path' => 'M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5 3.66 9.16 8.44 9.94v-7.03H7.9v-2.9h2.54V9.85c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.44 2.9h-2.34V22c4.78-.78 8.44-4.94 8.44-9.94Z'],
                            ['label' => 'Twitter', 'href' => 'https://twitter.com/YJennvi', 'path' => 'M22 5.9c-.7.3-1.5.5-2.3.7.8-.5 1.5-1.3 1.8-2.3-.8.5-1.7.8-2.6 1a4.1 4.1 0 0 0-7 3.8A11.6 11.6 0 0 1 3.4 4.6a4.2 4.2 0 0 0 1.3 5.5c-.6 0-1.3-.2-1.8-.5v.1c0 2 1.4 3.6 3.3 4a4.1 4.1 0 0 1-1.8.1 4.1 4.1 0 0 0 3.9 2.9A8.3 8.3 0 0 1 2 18.4a11.6 11.6 0 0 0 6.3 1.8c7.5 0 11.7-6.4 11.7-11.9v-.5c.8-.6 1.5-1.3 2-2.1Z'],
                            ['label' => 'YouTube', 'href' => '#', 'path' => 'M23 12s0-3.5-.4-5.2c-.3-1-1-1.7-2-2C18.9 4.3 12 4.3 12 4.3s-6.9 0-8.6.5c-1 .3-1.7 1-2 2C1 8.5 1 12 1 12s0 3.5.4 5.2c.3 1 1 1.7 2 2 1.7.5 8.6.5 8.6.5s6.9 0 8.6-.5c1-.3 1.7-1 2-2 .4-1.7.4-5.2.4-5.2ZM9.8 15.5V8.5l6.4 3.5-6.4 3.5Z'],
                            ['label' => 'Instagram', 'href' => 'https://www.instagram.com/younglifehaiti_jennvi/', 'path' => 'M12 2.2c2.7 0 3 0 4 .1 1 .1 1.6.2 2 .4.5.2.9.4 1.3.8.4.4.6.8.8 1.3.2.4.3 1 .4 2 .1 1 .1 1.3.1 4s0 3-.1 4c-.1 1-.2 1.6-.4 2-.2.5-.4.9-.8 1.3-.4.4-.8.6-1.3.8-.4.2-1 .3-2 .4-1 .1-1.3.1-4 .1s-3 0-4-.1c-1-.1-1.6-.2-2-.4a3.5 3.5 0 0 1-1.3-.8 3.5 3.5 0 0 1-.8-1.3c-.2-.4-.3-1-.4-2-.1-1-.1-1.3-.1-4s0-3 .1-4c.1-1 .2-1.6.4-2 .2-.5.4-.9.8-1.3.4-.4.8-.6 1.3-.8.4-.2 1-.3 2-.4 1-.1 1.3-.1 4-.1Zm0 1.8c-2.6 0-2.9 0-4 .1-.8 0-1.3.2-1.6.3-.4.1-.7.3-1 .6-.3.3-.5.6-.6 1-.1.3-.3.8-.3 1.6-.1 1.1-.1 1.4-.1 4s0 2.9.1 4c0 .8.2 1.3.3 1.6.1.4.3.7.6 1 .3.3.6.5 1 .6.3.1.8.3 1.6.3 1.1.1 1.4.1 4 .1s2.9 0 4-.1c.8 0 1.3-.2 1.6-.3.4-.1.7-.3 1-.6.3-.3.5-.6.6-1 .1-.3.3-.8.3-1.6.1-1.1.1-1.4.1-4s0-2.9-.1-4c0-.8-.2-1.3-.3-1.6a2.7 2.7 0 0 0-.6-1 2.7 2.7 0 0 0-1-.6c-.3-.1-.8-.3-1.6-.3-1.1-.1-1.4-.1-4-.1Zm0 3a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.8a3.2 3.2 0 1 0 0 6.4 3.2 3.2 0 0 0 0-6.4Zm5.2-2a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4Z'],
                        ];
                    @endphp
                    @foreach ($socials as $s)
                        <a href="{{ $s['href'] }}" aria-label="{{ $s['label'] }}" class="grid h-10 w-10 place-items-center rounded-full bg-white/10 text-white/80 transition hover:bg-[#7ec13d] hover:text-[#07395b]">
                            <svg viewBox="0 0 24 24" fill="currentColor" class="h-4.5 w-4.5"><path d="{{ $s['path'] }}" /></svg>
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-white">Menu</h3>
                <ul class="mt-5 space-y-3 text-sm">
                    <li><a href="/" class="transition hover:text-[#7ec13d]">Accueil</a></li>
                    <li><a href="{{ route('a-propos') }}" class="transition hover:text-[#7ec13d]">À propos de nous</a></li>
                    <li><a href="{{ route('leve-fonds') }}" class="transition hover:text-[#7ec13d]">Lève Fonds</a></li>
                    <li><a href="{{ route('gallery') }}" class="transition hover:text-[#7ec13d]">Galerie</a></li>
                    <li><a href="{{ route('contact') }}" class="transition hover:text-[#7ec13d]">Contactez-nous</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-white">Contact</h3>
                <ul class="mt-5 space-y-3 text-sm">
                    <li class="text-white/60">#10, Delmas 26, Port-au-Prince, Haïti</li>
                    <li><a href="tel:+50922279026" class="transition hover:text-[#7ec13d]">(+509) 22 27 9026</a></li>
                    <li><a href="mailto:smkdyounglifehaiti@gmail.com" class="transition hover:text-[#7ec13d]">smkdyounglifehaiti@gmail.com</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10 py-6">
        <p class="text-center text-xs text-white/50">&copy; {{ now()->year }} Young Life Haïti — Jennvi. Tous droits réservés.</p>
    </div>
</footer>
