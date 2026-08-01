<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClubResource\Pages;
use App\Models\Club;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClubResource extends Resource
{
    protected static ?string $model = Club::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $modelLabel = 'club';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->visibleTo(auth()->user());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('arrondissement_id')
                    ->label('Arrondissement')
                    ->relationship(
                        name: 'arrondissement',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => auth()->user()->isSuperAdmin()
                            ? $query
                            : $query->whereIn('zone_id', auth()->user()->zones()->pluck('zones.id')),
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->zone->name} — {$record->name}")
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Nom du club')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom du club')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('arrondissement.name')
                    ->label('Arrondissement')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('arrondissement.zone.name')
                    ->label('Zone')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('arrondissement_id')
                    ->label('Arrondissement')
                    ->relationship('arrondissement', 'name')
                    ->searchable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageClubs::route('/'),
        ];
    }
}
