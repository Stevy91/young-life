<?php

namespace App\Filament\Resources\ZoneResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ArrondissementsRelationManager extends RelationManager
{
    protected static string $relationship = 'arrondissements';

    protected static ?string $title = 'Arrondissements';

    protected static ?string $modelLabel = 'arrondissement';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label("Nom de l'arrondissement")
                    ->helperText('Ex : ARR HINCHE, ARR MIREBALAIS...')
                    ->required()
                    ->maxLength(255),
                // Only shown when editing: lets an admin move this
                // arrondissement to a different zone. On create it's implicit
                // (this zone), so the field would be redundant/confusing.
                Forms\Components\Select::make('zone_id')
                    ->label('Zone')
                    ->relationship('zone', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->hiddenOn('create'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Arrondissement')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('clubs_count')
                    ->label('Clubs')
                    ->counts('clubs')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
