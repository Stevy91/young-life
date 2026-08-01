<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArrondissementResource\Pages;
use App\Filament\Resources\ArrondissementResource\RelationManagers;
use App\Models\Arrondissement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ArrondissementResource extends Resource
{
    protected static ?string $model = Arrondissement::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $modelLabel = 'arrondissement';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('zone_id')
                    ->label('Zone')
                    ->relationship('zone', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Vous pouvez déplacer un arrondissement vers une autre zone à tout moment.'),
                Forms\Components\TextInput::make('name')
                    ->label("Nom de l'arrondissement")
                    ->helperText('Ex : ARR HINCHE, ARR MIREBALAIS...')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Arrondissement')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('zone.name')
                    ->label('Zone')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('clubs_count')
                    ->label('Clubs')
                    ->counts('clubs')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('zone_id')
                    ->label('Zone')
                    ->relationship('zone', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ClubsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArrondissements::route('/'),
            'create' => Pages\CreateArrondissement::route('/create'),
            'edit' => Pages\EditArrondissement::route('/{record}/edit'),
        ];
    }
}
