<?php

namespace App\Filament\Pages\Content;

use Filament\Forms;
use Filament\Forms\Form;

class GalleryPageContent extends AbstractPageContentPage
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Galerie';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Page Galerie';

    public static function pageKey(): string
    {
        return 'galerie';
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
                        Forms\Components\Textarea::make('hero_text')->label('Texte')->rows(2),
                    ]),

                Forms\Components\Section::make('Photos')
                    ->description('Ajoutez, réordonnez ou retirez des photos librement.')
                    ->schema([
                        Forms\Components\Repeater::make('photos')
                            ->label('')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Image')
                                    ->helperText('Taille recommandée : carrée, au moins 800 × 800 px. Toute image sera automatiquement recadrée en carré.')
                                    ->image()
                                    ->directory('page-content')
                                    ->maxSize(2048),
                            ])
                            ->addActionLabel('Ajouter une photo')
                            ->reorderable()
                            ->collapsed()
                            ->itemLabel(fn (): string => 'Photo')
                            ->defaultItems(0),
                    ]),
            ]);
    }
}
