<?php

namespace App\Filament\Pages\Content;

use Filament\Forms;
use Filament\Forms\Form;

class HomePageContent extends AbstractPageContentPage
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Accueil';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Page Accueil';

    public static function pageKey(): string
    {
        return 'home';
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('En-tête (hero)')
                    ->description("Le texte principal et les images qui défilent en fond sur la page d'accueil.")
                    ->schema([
                        Forms\Components\TextInput::make('hero_badge')
                            ->label('Petit badge au-dessus du titre')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hero_title')
                            ->label('Titre principal')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('hero_text')
                            ->label('Sous-titre')
                            ->rows(2),
                        Forms\Components\Repeater::make('hero_slides')
                            ->label('Images du diaporama')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Image')
                                    ->helperText('Taille recommandée : 1920 × 1080 px minimum, format paysage. Remplit tout l\'écran en fond (recadrage automatique).')
                                    ->image()
                                    ->directory('page-content')
                                    ->maxSize(2048),
                            ])
                            ->addActionLabel('Ajouter une image')
                            ->reorderable()
                            ->collapsed()
                            ->minItems(1)
                            ->itemLabel(fn (): string => 'Image'),
                    ]),

                Forms\Components\Section::make('Mission, Vision, Valeur')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Fieldset::make('Mission')
                            ->schema([
                                Forms\Components\TextInput::make('mission_title')->label('Titre')->required(),
                                Forms\Components\Textarea::make('mission_text')->label('Texte')->rows(3)->required(),
                            ]),
                        Forms\Components\Fieldset::make('Vision')
                            ->schema([
                                Forms\Components\TextInput::make('vision_title')->label('Titre')->required(),
                                Forms\Components\Textarea::make('vision_text')->label('Texte')->rows(3)->required(),
                            ]),
                        Forms\Components\Fieldset::make('Valeur')
                            ->schema([
                                Forms\Components\TextInput::make('value_title')->label('Titre')->required(),
                                Forms\Components\Textarea::make('value_text')->label('Texte')->rows(3)->required(),
                            ]),
                    ]),

                Forms\Components\Section::make('Section "À propos"')
                    ->schema([
                        Forms\Components\FileUpload::make('about_image')
                            ->label('Image')
                            ->helperText('Taille recommandée : environ 1000 × 750 px (format 4:3). Affichée à sa taille réelle, sans recadrage.')
                            ->image()
                            ->directory('page-content')
                            ->maxSize(2048),
                        Forms\Components\TextInput::make('about_eyebrow')->label('Sur-titre')->maxLength(255),
                        Forms\Components\TextInput::make('about_title')->label('Titre')->maxLength(255),
                        Forms\Components\Repeater::make('about_checklist')
                            ->label('Liste à puces')
                            ->schema([
                                Forms\Components\Textarea::make('text')->label('Texte')->rows(2)->required(),
                            ])
                            ->addActionLabel('Ajouter une puce')
                            ->reorderable()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['text'] ?? null),
                    ]),

                Forms\Components\Section::make('Bandeau "JennVi"')
                    ->schema([
                        Forms\Components\TextInput::make('cta_title')->label('Titre')->maxLength(255),
                        Forms\Components\Textarea::make('cta_text')->label('Texte')->rows(3),
                    ]),

                Forms\Components\Section::make('Nos programmes (camps)')
                    ->schema([
                        Forms\Components\TextInput::make('programs_eyebrow')->label('Sur-titre')->maxLength(255),
                        Forms\Components\TextInput::make('programs_title')->label('Titre')->maxLength(255),
                        Forms\Components\Textarea::make('programs_text')->label('Texte')->rows(2),
                        Forms\Components\Repeater::make('camp_highlights')
                            ->label('Cartes')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Image')
                                    ->helperText('Taille recommandée : 1200 × 900 px (format 4:3). Recadrée automatiquement en carte.')
                                    ->image()
                                    ->directory('page-content')
                                    ->maxSize(2048),
                                Forms\Components\TextInput::make('title')->label('Titre')->required()->maxLength(255),
                                Forms\Components\Textarea::make('text')->label('Texte')->rows(3)->required(),
                            ])
                            ->addActionLabel('Ajouter une carte')
                            ->reorderable()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                    ]),

                Forms\Components\Section::make('Témoignage')
                    ->schema([
                        Forms\Components\FileUpload::make('testimonial_image')
                            ->label('Image de fond')
                            ->helperText('Taille recommandée : 1920 × 1080 px minimum, format paysage. Utilisée en fond avec effet parallax.')
                            ->image()
                            ->directory('page-content')
                            ->maxSize(2048),
                        Forms\Components\Textarea::make('testimonial_text')->label('Citation')->rows(2),
                    ]),

                Forms\Components\Section::make('Aperçu galerie (bas de page)')
                    ->schema([
                        Forms\Components\Textarea::make('gallery_quote')->label('Citation finale')->rows(2),
                    ]),
            ]);
    }
}
