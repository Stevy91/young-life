<?php

namespace App\Filament\Resources\ArrondissementResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ClubsRelationManager extends RelationManager
{
    protected static string $relationship = 'clubs';

    protected static ?string $title = 'Clubs';

    protected static ?string $modelLabel = 'club';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom du club')
                    ->required()
                    ->maxLength(255),
                // Only shown when editing: lets an admin move this club to a
                // different arrondissement (any zone). On create it's implicit
                // (this arrondissement).
                Forms\Components\Select::make('arrondissement_id')
                    ->label('Arrondissement')
                    ->relationship('arrondissement', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->zone->name} — {$record->name}")
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
                    ->label('Nom du club')
                    ->searchable()
                    ->sortable(),
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
