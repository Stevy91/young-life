<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

class PageContentSeeder extends Seeder
{
    /**
     * Captures the content that was hardcoded in the 4 public Blade views
     * (home, leve-fonds, a-propos, galerie) at the moment they were switched
     * to read from PageContent — so the site doesn't go blank the moment an
     * admin opens the new "Pages du site" section.
     */
    public function run(): void
    {
        PageContent::updateOrCreate(['page' => 'home'], ['data' => [
            'hero_badge' => 'Young Life Haïti · Jennvi',
            'hero_title' => 'Aider les jeunes à développer leurs dons.',
            'hero_text' => 'Leurs capacités et leurs attitudes pour atteindre leur plein potentiel donné par Dieu.',
            'hero_slides' => [
                ['image' => 'page-content/slider/slide2.jpeg'],
                ['image' => 'page-content/slider/slide6.jpeg'],
                ['image' => 'page-content/slider/slide4.jpeg'],
            ],
            'mission_title' => 'Notre Mission',
            'mission_text' => "Introduire les adolescents du monde entier à Jésus-Christ et les aider à grandir dans leur foi.",
            'vision_title' => 'Notre Vision',
            'vision_text' => "Que chaque adolescent puisse avoir l'occasion de rencontrer Jésus-Christ et de Le suivre.",
            'value_title' => 'Notre Valeur',
            'value_text' => 'Rechercher et développer des approches innovantes pour atteindre les jeunes.',
            'about_eyebrow' => 'À propos de notre organisation',
            'about_title' => "Gagner le droit d'être écouté",
            'about_image' => 'page-content/about/about_img.png',
            'about_checklist' => [
                ['text' => "Young Life est un ministère chrétien qui s'adresse aux collégiens, lycéens et étudiants dans les 50 États des États-Unis et dans plus de 100 pays à travers le monde."],
                ['text' => "Encourager notre personnel et nos bénévoles dans leur santé personnelle et spirituelle afin que nous puissions exercer notre ministère à partir d'une relation cohérente et croissante avec Christ et ses disciples."],
            ],
            'cta_title' => 'JennVi',
            'cta_text' => "Encourager notre personnel et nos bénévoles dans leur santé personnelle et spirituelle afin que nous puissions exercer notre ministère à partir d'une relation cohérente et croissante avec Christ et ses disciples.",
            'programs_eyebrow' => 'Nos programmes',
            'programs_title' => 'Venez participer avec nous',
            'programs_text' => "Young Life est un ministère chrétien qui s'adresse aux collégiens, lycéens et étudiants dans les 50 États des États-Unis et dans plus de 100 pays à travers le monde.",
            'camp_highlights' => [
                ['image' => 'page-content/blog/e2.jpg', 'title' => 'Camps Konbit', 'text' => "Du 28 juillet au 31 août, la Young Life Haïti organise une série de 4 camps baptisée Konbit. Nous comptons évangéliser des milliers de jeunes à travers ces sessions."],
                ['image' => 'page-content/blog/e3.jpg', 'title' => 'Camps Prière', 'text' => "Plus de 200 jeunes ont donné leurs vies à Christ durant les séries de camp organisées par la Young Life Haïti au camp de prière à Maïssade."],
                ['image' => 'page-content/blog/e1.jpg', 'title' => 'Camps Mifylh', 'text' => "Le Ministère des Femmes de la Young Life Haïti (MIFYLH) organise un camp de jeunesse à Maïssade réunissant de nombreuses jeunes filles à travers le pays."],
            ],
            'testimonial_image' => 'page-content/slider/slide2.jpeg',
            'testimonial_text' => 'Pleine vie, aventure, amusement, relations, but, Jésus.',
            'gallery_quote' => "Young Life est l'endroit où je peux être moi-même — brisé et tout — et je peux avoir confiance que je serai aimé et accepté pour qui je suis.",
        ]]);

        PageContent::updateOrCreate(['page' => 'leve-fonds'], ['data' => [
            'banner_image' => 'page-content/about/banner2025.jpg',
            'eyebrow' => 'Semèn Leve Fon',
            'title' => 'Lève Fonds',
            'intro_text' => 'Soutenez le bloc de votre zone ou la direction nationale par MonCash ou NatCash.',
            'stats_eyebrow' => 'Ansanm nou fò',
            'stats_title' => 'Tout sa nou reyalize pou ane 2022',
            'stats' => [
                ['value' => '35', 'label' => 'Kan'],
                ['value' => '77', 'label' => 'Moun ki restore'],
                ['value' => '323', 'label' => 'Moun ki konvèti'],
                ['value' => '1 237', 'label' => 'Nouvo manb'],
            ],
            'blocks_eyebrow' => 'Blòk nou yo',
            'blocks_title' => 'Chwazi blòk ou pou kontribye',
            'blocks_text' => 'Rele nou oswa fè depo an pa MonCash oswa NatCash ak nimewo ki atribye ak zòn ou an.',
            'donation_blocks' => [
                ['name' => 'Blòk Sidwès', 'progress' => '9.74', 'awondisman' => "Koray\nJeremi\nAnsavo\nOkay\nAken\nBaradè", 'moncash' => '(509) 3845-1314', 'natcash' => '(509) 3665-6093'],
                ['name' => 'Blòk Sidès', 'progress' => '10', 'awondisman' => "Miragwan\nBèlans\nBenè\nJakmèl\nLeyogàn", 'moncash' => '(509) 4479-2926', 'natcash' => '(509) 3533-4739'],
                ['name' => 'Blòk Sant', 'progress' => '44', 'awondisman' => "Mibalè\nEnch\nLaskawobas", 'moncash' => '(509) 3734-4504', 'natcash' => '(509) 3524-3245'],
                ['name' => 'Blòk Nòdwès', 'progress' => '44', 'awondisman' => "Gonayiv\nPòdepè\nSenlwi di Nò\nGwo Mòn\nMòl Sen Nikola", 'moncash' => '(509) 4838-7655', 'natcash' => '(509) 3267-3731'],
                ['name' => 'Blòk Nòdès', 'progress' => '44', 'awondisman' => "Kap Ayisyen\nTwou di Nò\nGran Rivyè di Nò\nSen Rafayèl", 'moncash' => '(509) 3757-1378', 'natcash' => '(509) 3546-3581'],
                ['name' => 'Blòk Lwès', 'progress' => '44', 'awondisman' => "Pòtoprens\nAkayè\nKwadèboukè\nLagonav", 'moncash' => '(509) 4910-3626', 'natcash' => '(509) 4183-4099'],
                ['name' => 'Rejyon Ayiti Sid', 'progress' => '44', 'awondisman' => "Metwo Sidwès\nMetwo Sidès\nMetwo Lwès", 'moncash' => '(509) 4813-9160', 'natcash' => '(509) 3322-1246'],
                ['name' => 'Direksyon Nasyonal', 'progress' => '100', 'awondisman' => "Kan Rapadou\nKonpayon Plis\nEntandans ak Jenewozite\nKominikasyon\nOperasyon ak Finans", 'moncash' => '(509) 4825-0385', 'natcash' => '(509) 4315-3129'],
            ],
            'regions_eyebrow' => 'Rejyon yo',
            'regions_title' => 'Kat rejyon Ayiti',
            'region_maps' => [
                ['image' => 'page-content/slider/rejyonayiti.jpg', 'label' => 'Ayiti'],
                ['image' => 'page-content/slider/rejyonayitino.jpg', 'label' => 'Nò'],
                ['image' => 'page-content/slider/rejyonayitisid.jpg', 'label' => 'Sid'],
                ['image' => 'page-content/slider/sant.jpg', 'label' => 'Sant'],
                ['image' => 'page-content/slider/sides.jpg', 'label' => 'Sidès'],
                ['image' => 'page-content/slider/sidwes.jpg', 'label' => 'Sidwès'],
                ['image' => 'page-content/slider/lwes.jpg', 'label' => 'Lwès'],
                ['image' => 'page-content/slider/nodes.jpg', 'label' => 'Nòdès'],
                ['image' => 'page-content/slider/nodwes.jpg', 'label' => 'Nòdwès'],
            ],
        ]]);

        PageContent::updateOrCreate(['page' => 'a-propos'], ['data' => [
            'hero_eyebrow' => 'Qui sommes-nous',
            'hero_title' => 'À Propos de Young Life Haïti',
            'about_eyebrow' => 'À propos de notre organisation',
            'about_title' => 'Young Life Haïti',
            'about_text' => "Young Life est un ministère chrétien qui s'adresse aux collégiens, lycéens et étudiants dans les 50 États des États-Unis et dans plus de 100 pays à travers le monde.",
            'about_image' => 'page-content/about/about-us.jpg',
            'about_paragraph' => "Encourager notre personnel et nos bénévoles dans leur santé personnelle et spirituelle afin que nous puissions exercer notre ministère à partir d'une relation cohérente et croissante avec Christ et ses disciples.",
            'quote_text' => "Young Life est l'endroit où je peux être moi-même — brisé et tout — et je peux avoir confiance que je serai aimé et accepté pour qui je suis.",
            'values' => [
                ['text' => 'Rechercher'],
                ['text' => 'Introduire'],
                ['text' => 'Développer'],
                ['text' => 'Rencontrer'],
                ['text' => 'Innover'],
            ],
            'team_eyebrow' => "L'équipe",
            'team_title' => 'Notre Équipe',
            'team_members' => [
                ['name' => 'Chedrick Caneus', 'role' => 'Directeur Général', 'photo' => 'page-content/team/che1.jpg', 'facebook' => 'https://web.facebook.com/chedrick.caneus'],
                ['name' => 'Noyo Cherisma', 'role' => 'Développeur Web', 'photo' => 'page-content/team/noy1.jpg', 'facebook' => 'https://web.facebook.com/noyo.cherisma'],
                ['name' => 'Bouzy Evens', 'role' => 'Responsable de gestion', 'photo' => null, 'facebook' => 'https://web.facebook.com/bouzy.evens'],
                ['name' => 'Wadny Cherisma', 'role' => 'Responsable de gestion', 'photo' => null, 'facebook' => 'https://web.facebook.com/Wad.Cherisma'],
                ['name' => 'Sandra Exuma', 'role' => 'Responsable de gestion', 'photo' => 'page-content/team/san1.jpg', 'facebook' => 'https://web.facebook.com/sandra.exuma.5'],
                ['name' => 'Lisberthe Exuma', 'role' => 'Responsable de gestion', 'photo' => 'page-content/team/lisb1.jpg', 'facebook' => 'https://web.facebook.com/lisberthe.caneusexuma'],
            ],
        ]]);

        $galleryFiles = [
            'g1.jpg', 'M6.jpg', 'M20.jpeg', 'M21.jpeg', 'M22.jpeg', 'M23.jpeg', 'M24.jpeg', 'M17.jpg', 'M12.jpg', 'g2.jpg',
            'M8.jpg', 'M25.jpeg', 'M9.jpg', 'M26.jpeg', 'M13.jpg', 'M27.jpeg', 'M28.jpeg', 'M29.jpeg', 'M10.jpg', 'M30.jpeg',
            'M11.jpg', 'M18.jpg', 'M7.jpg', 'M31.jpeg', 'M19.jpg', 'g3.jpg', 'M111.jpg', 'M112.jpg', 'M113.jpg', 'M114.jpg',
            'M115.jpg', 'M116.jpg', 'M117.jpg', 'M118.jpg', 'M119.jpg', 'M120.jpg', 'M121.jpg', 'M122.jpg', 'g6.jpg', 'M123.jpg',
            'M124.jpg', 'M125.jpg', 'g5.jpg', 'M126.jpg', 'M127.jpg', 'M128.jpg', 'M129.jpg', 'g7.jpg', 'M130.jpg', 'M131.jpg',
            'M132.jpg', 'M133.jpg', 'g10.jpg', 'M134.jpg', 'M135.jpg', 'g9.jpg', 'M136.jpg',
        ];

        PageContent::updateOrCreate(['page' => 'galerie'], ['data' => [
            'hero_eyebrow' => 'En images',
            'hero_title' => 'Galerie',
            'hero_text' => "Nous serions ravis d'entendre et de partager avec d'autres comment votre amour pour Young Life vous a amené à vous engager dans la mission, et comment cela vous a béni.",
            'photos' => collect($galleryFiles)
                ->map(fn (string $file) => ['image' => "page-content/portfolio/{$file}"])
                ->all(),
        ]]);
    }
}
