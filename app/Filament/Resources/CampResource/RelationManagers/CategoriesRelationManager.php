<?php

namespace App\Filament\Resources\CampResource\RelationManagers;

use App\Models\CampCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';

    protected static ?string $title = 'Rôles & quotas';

    protected static ?string $modelLabel = 'rôle';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom du rôle')
                    ->helperText('Ex : Conseiller, Responsable, Campeur / Jeunes...')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('quota')
                    ->label('Quota (optionnel)')
                    ->helperText('Nombre maximum de personnes attendues dans ce rôle.')
                    ->numeric()
                    ->minValue(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Rôle'),
                Tables\Columns\TextColumn::make('registrations_count')
                    ->label('Inscrits / Quota')
                    ->counts('registrations')
                    ->formatStateUsing(fn (CampCategory $record, $state) => $record->quota !== null
                        ? "{$state} sur {$record->quota}"
                        : (string) $state)
                    ->badge()
                    ->color(fn (CampCategory $record) => $record->quota !== null && $record->registrations_count >= $record->quota
                        ? 'danger'
                        : 'success'),
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
            ]);
    }
}
