<?php

namespace App\Filament\Pages\Content;

use Filament\Forms;
use Filament\Forms\Form;

class LeveFondsPageContent extends AbstractPageContentPage
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Lève Fonds';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Page Lève Fonds';

    public static function pageKey(): string
    {
        return 'leve-fonds';
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('En-tête')
                    ->schema([
                        Forms\Components\FileUpload::make('banner_image')
                            ->label('Bannière')
                            ->helperText("Cette image contient déjà son propre texte/dates — elle s'affiche en entier, sans recadrage.")
                            ->image()
                            ->directory('page-content')
                            ->maxSize(2048),
                        Forms\Components\TextInput::make('eyebrow')->label('Sur-titre')->maxLength(255),
                        Forms\Components\TextInput::make('title')->label('Titre')->maxLength(255),
                        Forms\Components\Textarea::make('intro_text')->label("Texte d'introduction")->rows(2),
                    ]),

                Forms\Components\Section::make('Statistiques')
                    ->schema([
                        Forms\Components\TextInput::make('stats_eyebrow')->label('Sur-titre')->maxLength(255),
                        Forms\Components\TextInput::make('stats_title')->label('Titre')->maxLength(255),
                        Forms\Components\Repeater::make('stats')
                            ->label('Chiffres')
                            ->schema([
                                Forms\Components\TextInput::make('value')->label('Valeur')->required(),
                                Forms\Components\TextInput::make('label')->label('Libellé')->required(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Ajouter un chiffre')
                            ->reorderable()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null),
                    ]),

                Forms\Components\Section::make('Blocs de dons')
                    ->schema([
                        Forms\Components\TextInput::make('blocks_eyebrow')->label('Sur-titre')->maxLength(255),
                        Forms\Components\TextInput::make('blocks_title')->label('Titre')->maxLength(255),
                        Forms\Components\Textarea::make('blocks_text')->label('Texte')->rows(2),
                        Forms\Components\Repeater::make('donation_blocks')
                            ->label('Blocs')
                            ->schema([
                                Forms\Components\TextInput::make('name')->label('Nom du bloc')->required()->maxLength(255),
                                Forms\Components\TextInput::make('progress')
                                    ->label('Objectif atteint (%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),
                                Forms\Components\Textarea::make('awondisman')
                                    ->label('Zones couvertes (une par ligne)')
                                    ->rows(4)
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('moncash')->label('Numéro MonCash')->required(),
                                Forms\Components\TextInput::make('natcash')->label('Numéro NatCash')->required(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Ajouter un bloc')
                            ->reorderable()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ]),

                Forms\Components\Section::make('Galerie régionale')
                    ->schema([
                        Forms\Components\TextInput::make('regions_eyebrow')->label('Sur-titre')->maxLength(255),
                        Forms\Components\TextInput::make('regions_title')->label('Titre')->maxLength(255),
                        Forms\Components\Repeater::make('region_maps')
                            ->label('Images')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Image')
                                    ->helperText('Taille recommandée : 1200 × 900 px (format 4:3). Recadrée automatiquement en vignette.')
                                    ->image()
                                    ->directory('page-content')
                                    ->maxSize(2048),
                                Forms\Components\TextInput::make('label')->label('Étiquette')->required()->maxLength(255),
                            ])
                            ->columns(2)
                            ->addActionLabel('Ajouter une image')
                            ->reorderable()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null),
                    ]),
            ]);
    }
}
