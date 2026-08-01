<?php

namespace App\Filament\Pages\Content;

use Filament\Forms;
use Filament\Forms\Form;

class AboutPageContent extends AbstractPageContentPage
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'À Propos';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Page À Propos';

    public static function pageKey(): string
    {
        return 'a-propos';
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('En-tête')
                    ->schema([
                        Forms\Components\TextInput::make('hero_eyebrow')->label('Sur-titre')->maxLength(255),
                        Forms\Components\TextInput::make('hero_title')->label('Titre')->maxLength(255),
                    ]),

                Forms\Components\Section::make('À propos de notre organisation')
                    ->schema([
                        Forms\Components\TextInput::make('about_eyebrow')->label('Sur-titre')->maxLength(255),
                        Forms\Components\TextInput::make('about_title')->label('Titre')->maxLength(255),
                        Forms\Components\Textarea::make('about_text')->label("Texte d'introduction")->rows(2),
                        Forms\Components\FileUpload::make('about_image')
                            ->label('Image')
                            ->helperText('Taille recommandée : environ 1000 × 750 px (format 4:3). Affichée à sa taille réelle, sans recadrage.')
                            ->image()
                            ->directory('page-content')
                            ->maxSize(2048),
                        Forms\Components\Textarea::make('about_paragraph')->label('Paragraphe')->rows(3),
                        Forms\Components\Textarea::make('quote_text')->label('Citation')->rows(2),
                        Forms\Components\Repeater::make('values')
                            ->label('Nos valeurs')
                            ->schema([
                                Forms\Components\TextInput::make('text')->label('Valeur')->required()->maxLength(255),
                            ])
                            ->addActionLabel('Ajouter une valeur')
                            ->reorderable()
                            ->grid(3),
                    ]),

                Forms\Components\Section::make('Équipe')
                    ->schema([
                        Forms\Components\TextInput::make('team_eyebrow')->label('Sur-titre')->maxLength(255),
                        Forms\Components\TextInput::make('team_title')->label('Titre')->maxLength(255),
                        Forms\Components\Repeater::make('team_members')
                            ->label('Membres')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->label('Photo (optionnelle — initiales affichées si absente)')
                                    ->helperText('Taille recommandée : carrée, au moins 600 × 600 px (idéal 800 × 800 px).')
                                    ->image()
                                    ->directory('page-content')
                                    ->maxSize(2048),
                                Forms\Components\TextInput::make('name')->label('Nom')->required()->maxLength(255),
                                Forms\Components\TextInput::make('role')->label('Rôle')->required()->maxLength(255),
                                Forms\Components\TextInput::make('facebook')->label('Lien Facebook')->url()->maxLength(255),
                            ])
                            ->columns(2)
                            ->addActionLabel('Ajouter un membre')
                            ->reorderable()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ]),
            ]);
    }
}
