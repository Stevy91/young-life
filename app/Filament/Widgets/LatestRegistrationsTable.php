<?php

namespace App\Filament\Widgets;

use App\Models\Registration;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LatestRegistrationsTable extends BaseWidget
{
    protected static ?string $heading = 'Dernières inscriptions';

    protected static ?int $sort = 8;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Registration::query()
                    ->visibleTo(Auth::user())
                    ->with(['camp', 'campCategory'])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('nom')->label('Nom')->searchable(),
                Tables\Columns\TextColumn::make('camp.name')->label('Camp'),
                Tables\Columns\TextColumn::make('campCategory.name')->label('Rôle')->badge(),
                Tables\Columns\TextColumn::make('sexe')->label('Sexe')->badge(),
                Tables\Columns\TextColumn::make('statut')->label('Statut')->badge(),
                Tables\Columns\TextColumn::make('created_at')->label('Inscrit le')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
