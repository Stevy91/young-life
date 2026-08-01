<?php

namespace App\Filament\Resources;

use App\Enums\CampStatus;
use App\Filament\Resources\CampResource\Pages;
use App\Filament\Resources\CampResource\RelationManagers;
use App\Models\Arrondissement;
use App\Models\Camp;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CampResource extends Resource
{
    protected static ?string $model = Camp::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'Tous les camps';

    protected static ?string $modelLabel = 'camp';

    // Sorted after the dynamic per-zone navigation items (see
    // AdminPanelProvider) so zone browsing comes first in the sidebar; this
    // stays as the cross-zone search/management view.
    protected static ?int $navigationSort = 100;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->visibleTo(auth()->user());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du camp')
                    ->columns(2)
                    // Only Super Admins may change a camp's own info (CampPolicy);
                    // zone-scoped users can still open this page for the
                    // Registrations tab, but see these fields read-only.
                    ->disabled(fn () => ! auth()->user()->isSuperAdmin())
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du camp')
                            ->helperText('Ex : Konbit 3, Camp Prière II, K1 Nord-Ouest...')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('zone_id')
                            ->label('Zone')
                            ->default(fn () => request()->integer('zone_id') ?: null)
                            ->relationship(name: 'zone', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('statut')
                            ->label('Statut')
                            ->options(CampStatus::class)
                            ->default(CampStatus::Brouillon)
                            ->required(),
                        Forms\Components\DatePicker::make('date_debut')
                            ->label('Date de début')
                            ->native(false),
                        Forms\Components\DatePicker::make('date_fin')
                            ->label('Date de fin')
                            ->native(false)
                            ->afterOrEqual('date_debut'),
                        Forms\Components\TextInput::make('nb_nuits')
                            ->label('Nombre de nuits')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('capacite')
                            ->label('Capacité (optionnel)')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Camp')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('zone.name')
                    ->label('Zone')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_debut')
                    ->label('Début')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_fin')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('registrations_count')
                    ->label('Inscrits')
                    ->counts('registrations')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('zone_id')
                    ->label('Zone')
                    ->relationship('zone', 'name'),
                Tables\Filters\SelectFilter::make('statut')
                    ->label('Statut')
                    ->options(CampStatus::class),
            ])
            ->actions([
                Tables\Actions\Action::make('export_pdf')
                    ->label('Exporter PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(fn (Camp $record) => static::exportRegistrationsPdf($record)),
                Tables\Actions\Action::make('duplicate')
                    ->label('Dupliquer')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Dupliquer ce camp')
                    ->modalDescription('Crée un nouveau camp (nouvelle saison) avec le même nom, la même zone et les mêmes rôles/quotas. Les dates seront à définir et aucun inscrit ne sera copié.')
                    ->action(fn (Camp $record) => redirect(static::getUrl('edit', ['record' => static::duplicate($record)]))),
                Tables\Actions\Action::make('toggle_archive')
                    ->label(fn (Camp $record) => $record->statut === CampStatus::Archive ? 'Désarchiver' : 'Archiver')
                    ->icon(fn (Camp $record) => $record->statut === CampStatus::Archive ? 'heroicon-o-archive-box-x-mark' : 'heroicon-o-archive-box')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(fn (Camp $record) => static::toggleArchive($record)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * $arrondissement narrows the printout to just that arrondissement's
     * registrants (see the "Imprimer" popup on ZoneCamps) — leave it null
     * for the full camp list.
     */
    public static function exportRegistrationsPdf(Camp $camp, ?Arrondissement $arrondissement = null): StreamedResponse
    {
        $camp->load([
            'zone',
            'categories' => fn ($query) => $query->withCount('registrations'),
        ]);

        $registrations = $camp->registrations()
            ->with(['club', 'arrondissement', 'campCategory'])
            ->when($arrondissement, fn (Builder $query) => $query->where('arrondissement_id', $arrondissement->id))
            ->get();

        $pdf = Pdf::loadView('pdf.registrations-list', [
            'camp' => $camp,
            'registrations' => $registrations,
            'arrondissement' => $arrondissement,
        ])->setPaper('a3', 'portrait');

        $filename = $arrondissement
            ? "liste-{$camp->slug}-" . Str::slug($arrondissement->name) . '.pdf'
            : "liste-{$camp->slug}.pdf";

        // Livewire only recognizes StreamedResponse/BinaryFileResponse as a
        // real file download; Pdf::download() returns a plain Response, whose
        // binary body Livewire would otherwise try (and fail) to JSON-encode.
        return response()->streamDownload(
            fn () => print ($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf'],
        );
    }

    /**
     * Season rollover: camps recur every year under the same name (Konbit I,
     * Konbit II...). This clones the name/zone/roles+quotas into a fresh
     * Brouillon camp with blank dates and zero registrations — $camp itself
     * and its history are never touched.
     */
    public static function duplicate(Camp $camp): Camp
    {
        $new = Camp::create([
            'name' => $camp->name,
            'zone_id' => $camp->zone_id,
            'nb_nuits' => $camp->nb_nuits,
            'capacite' => $camp->capacite,
            'statut' => CampStatus::Brouillon,
        ]);

        // Camp::booted() seeds the DEFAULT_CATEGORIES on every new camp;
        // replace them with $camp's actual roles/quotas (which may have been
        // renamed, removed, or extended beyond the defaults).
        $new->categories()->delete();

        foreach ($camp->categories as $category) {
            $new->categories()->create([
                'name' => $category->name,
                'quota' => $category->quota,
            ]);
        }

        return $new;
    }

    /**
     * One-click season close/reopen: Archivé removes a finished camp from
     * the zone dashboard's default view (see ZoneCamps::getCamps()) without
     * touching its data. Reopening restores it to Ouvert.
     */
    public static function toggleArchive(Camp $camp): Camp
    {
        $camp->update([
            'statut' => $camp->statut === CampStatus::Archive ? CampStatus::Ouvert : CampStatus::Archive,
        ]);

        return $camp;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CategoriesRelationManager::class,
            RelationManagers\RegistrationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCamps::route('/'),
            'create' => Pages\CreateCamp::route('/create'),
            'edit' => Pages\EditCamp::route('/{record}/edit'),
        ];
    }
}
