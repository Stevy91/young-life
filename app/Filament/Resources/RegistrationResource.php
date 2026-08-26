<?php

namespace App\Filament\Resources;

use App\Enums\CampStatus;
use App\Enums\RegistrationStatut;
use App\Enums\Sexe;
use App\Filament\Resources\RegistrationResource\Pages;
use App\Models\Arrondissement;
use App\Models\Camp;
use App\Models\CampCategory;
use App\Models\Club;
use App\Models\Registration;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Toutes les inscriptions';

    protected static ?string $modelLabel = 'inscription';

    protected static ?int $navigationSort = 11;

    /**
     * Legacy "Type de Responsable" preset options, shown only when the
     * selected category looks like a "Responsable" one (see
     * categorySuggestsResponsable()).
     */
    private const MONTHS = [
        1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr',
        5 => 'Mai', 6 => 'Juin', 7 => 'Juil', 8 => 'Août',
        9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc',
    ];

    private const TYPES_RESPONSABLE = [
        "Equipe d'assignation",
        'Conseiller',
        'Leader',
        'Responsable de voyage',
        'Senior Stafff',
        'SMKD',
        'Chauffeur',
        'Animation',
        'Life Saver',
        "Coeur d'adoration",
        'Membre de famille',
        'Calpo / Work Crew',
        'Invité Adulte',
    ];

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->visibleTo(auth()->user());
    }

    private static function categorySuggestsResponsable(?int $categoryId): bool
    {
        if (! $categoryId) {
            return false;
        }

        $name = CampCategory::find($categoryId)?->name;

        return $name && str_contains(mb_strtolower($name), 'responsable');
    }

    /**
     * A category with no quota set is unlimited. $excludeRegistrationId lets
     * an edit form keep a registration's own (already-counted) slot without
     * locking the record out of the category it already occupies.
     */
    private static function categoryHasCapacity(?int $categoryId, ?int $excludeRegistrationId = null): bool
    {
        if (! $categoryId) {
            return true;
        }

        $category = CampCategory::find($categoryId);

        if (! $category || $category->quota === null) {
            return true;
        }

        $count = Registration::where('camp_category_id', $categoryId)
            ->when($excludeRegistrationId, fn (Builder $query) => $query->whereKeyNot($excludeRegistrationId))
            ->count();

        return $count < $category->quota;
    }

    /**
     * A camp only accepts new registrations while it's "Ouvert" — Brouillon
     * (not published yet) and Fermé (closed) both block adding new people,
     * matching what those statuses are supposed to mean. Super Admin can
     * still add to a non-open camp for edge cases (late arrival, correction
     * after closing), and editing an already-existing registration is never
     * blocked by this — only creating a new one.
     */
    private static function campAcceptsNewRegistrations(?Camp $camp): bool
    {
        if (! $camp) {
            return true;
        }

        return auth()->user()?->isSuperAdmin() || $camp->statut === CampStatus::Ouvert;
    }

    /**
     * The Jour/Mois/Année selects don't map to a real column (dehydrated
     * false) — this recomputes the real date_naissance field every time one
     * of them changes, and blanks it back out if the combination is invalid
     * (e.g. 31 février) rather than saving a wrong date.
     */
    private static function syncDateNaissance(Set $set, Get $get): void
    {
        $day = $get('date_naissance_day');
        $month = $get('date_naissance_month');
        $year = $get('date_naissance_year');

        if (! $day || ! $month || ! $year) {
            return;
        }

        if (! checkdate((int) $month, (int) $day, (int) $year)) {
            $set('date_naissance', null);

            return;
        }

        $set('date_naissance', \Illuminate\Support\Carbon::create((int) $year, (int) $month, (int) $day)->toDateString());
    }

    /**
     * Shared field schema, reused by the standalone resource form and by
     * CampResource's RegistrationsRelationManager so the two never drift
     * apart. Pass the owning $camp when it's already fixed (relation
     * manager); leave it null to show a live camp_id picker instead
     * (standalone resource) that the category/arrondissement fields react to.
     */
    public static function getFormSchema(?Camp $camp = null): array
    {
        return [
            Forms\Components\Section::make('Campeur')
                ->columns(2)
                ->schema([
                    ...($camp === null ? [
                        Forms\Components\Select::make('camp_id')
                            ->label('Camp')
                            ->relationship('camp', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('camp_category_id', null))
                            ->columnSpanFull(),
                    ] : []),
                    Forms\Components\TextInput::make('nom')
                        ->label('Nom complet')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Select::make('camp_category_id')
                        ->label('Rôle')
                        // Without this, leaving the field empty (the only
                        // option once every role gets disabled on a closed
                        // camp) skipped the closure rule below entirely —
                        // an unselected value never triggered it, so a
                        // registration could still be submitted with no
                        // category and no camp-status check applied at all.
                        ->required()
                        ->options(function (Get $get) use ($camp) {
                            $campId = $camp?->id ?? $get('camp_id');

                            if (! $campId) {
                                return [];
                            }

                            return CampCategory::where('camp_id', $campId)
                                ->withCount('registrations')
                                ->get()
                                ->mapWithKeys(fn (CampCategory $category) => [
                                    $category->id => $category->quota !== null
                                        ? "{$category->name} ({$category->registrations_count}/{$category->quota})"
                                        : $category->name,
                                ]);
                        })
                        ->disableOptionWhen(function (string $value, ?Registration $record): bool {
                            if (! self::categoryHasCapacity((int) $value, $record?->id)) {
                                return true;
                            }

                            // Only a brand new registration is blocked by camp status —
                            // an existing one stays editable even if the camp closed since.
                            if ($record !== null) {
                                return false;
                            }

                            return ! self::campAcceptsNewRegistrations(CampCategory::find((int) $value)?->camp);
                        })
                        ->helperText(fn (Get $get) => ($camp?->id ?? $get('camp_id')) ? null : "Choisissez d'abord un camp.")
                        ->searchable()
                        ->live()
                        ->rules([
                            fn (?Registration $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($record) {
                                if (! self::categoryHasCapacity((int) $value, $record?->id)) {
                                    $fail('Ce rôle a atteint son quota maximum de participants.');

                                    return;
                                }

                                if ($record !== null) {
                                    return;
                                }

                                $campOfCategory = CampCategory::find((int) $value)?->camp;

                                if (! self::campAcceptsNewRegistrations($campOfCategory)) {
                                    $fail("Ce camp n'est pas ouvert aux inscriptions (statut : {$campOfCategory->statut->getLabel()}).");
                                }
                            },
                        ]),
                    Forms\Components\Select::make('role_responsable')
                        ->label('Type de responsable')
                        ->options(array_combine(self::TYPES_RESPONSABLE, self::TYPES_RESPONSABLE))
                        ->searchable()
                        ->columnSpanFull()
                        ->visible(fn (Get $get) => self::categorySuggestsResponsable($get('camp_category_id')))
                        ->dehydrated(fn (Get $get) => self::categorySuggestsResponsable($get('camp_category_id'))),
                    Forms\Components\Select::make('sexe')
                        ->label('Sexe')
                        ->options(Sexe::class),
                    Forms\Components\Fieldset::make('Date de naissance')
                        ->columns(3)
                        ->schema([
                            Forms\Components\Select::make('date_naissance_day')
                                ->label('Jour')
                                ->options(array_combine(range(1, 31), range(1, 31)))
                                ->live()
                                ->dehydrated(false)
                                ->afterStateUpdated(fn (Set $set, Get $get) => self::syncDateNaissance($set, $get)),
                            Forms\Components\Select::make('date_naissance_month')
                                ->label('Mois')
                                ->options(self::MONTHS)
                                ->live()
                                ->dehydrated(false)
                                ->afterStateUpdated(fn (Set $set, Get $get) => self::syncDateNaissance($set, $get)),
                            Forms\Components\Select::make('date_naissance_year')
                                ->label('Année')
                                ->options(array_combine(range(now()->year, now()->year - 100), range(now()->year, now()->year - 100)))
                                ->live()
                                ->dehydrated(false)
                                ->afterStateUpdated(fn (Set $set, Get $get) => self::syncDateNaissance($set, $get)),
                        ]),
                    Forms\Components\Hidden::make('date_naissance')
                        ->afterStateHydrated(function (?string $state, Set $set): void {
                            if (blank($state)) {
                                return;
                            }

                            $date = \Illuminate\Support\Carbon::parse($state);
                            $set('date_naissance_day', $date->day);
                            $set('date_naissance_month', $date->month);
                            $set('date_naissance_year', $date->year);
                        }),
                    Forms\Components\TextInput::make('lieu_naissance')
                        ->label('Lieu de naissance')
                        ->maxLength(255),
                    Forms\Components\Select::make('statut')
                        ->label('Statut')
                        ->options(RegistrationStatut::class),
                    Forms\Components\TextInput::make('telephone')
                        ->label('Téléphone')
                        ->tel()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('nif_cin')
                        ->label('Nif / Cin')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('adresse')
                        ->label('Adresse')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Affiliation')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('arrondissement_id')
                        ->label('Arrondissement')
                        ->options(function () use ($camp) {
                            $zoneId = $camp?->zone_id;

                            return Arrondissement::when($zoneId, fn ($q) => $q->where('zone_id', $zoneId))
                                ->orderBy('name')
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('club_id', null)),
                    Forms\Components\Select::make('club_id')
                        ->label('Club')
                        ->options(function (Get $get) {
                            $arrondissementId = $get('arrondissement_id');

                            return $arrondissementId
                                ? Club::where('arrondissement_id', $arrondissementId)->orderBy('name')->pluck('name', 'id')
                                : [];
                        })
                        ->helperText(fn (Get $get) => $get('arrondissement_id') ? null : "Choisissez d'abord un arrondissement.")
                        ->searchable(),
                    Forms\Components\TextInput::make('leader')
                        ->label('Leader')
                        ->maxLength(255),
                ]),
            Forms\Components\FileUpload::make('photo')
                ->label('Photo')
                ->image()
                ->directory('registrations')
                ->avatar(),
            Forms\Components\Section::make("Renseignements sur l'événement")
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('campus')
                        ->label('Campus')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('adresse_campus')
                        ->label('Adresse du campus')
                        ->maxLength(255),
                    Forms\Components\Select::make('camp_de_jour')
                        ->label('Camp de jour')
                        ->options(['1' => 'Oui', '0' => 'Non']),
                    Forms\Components\Select::make('type_camp')
                        ->label('Type de Camps')
                        ->options([
                            'Retraite' => 'Retraite',
                            'Famille' => 'Famille',
                            'Discipolat' => 'Discipolat',
                            'Etendage' => 'Etendage',
                            'Leadership' => 'Leadership',
                            'Croissance Spirituelle' => 'Croissance Spirituelle',
                            'Formation' => 'Formation',
                            'Autres' => 'Autres',
                        ]),
                ]),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema(static::getFormSchema());
    }

    /**
     * Shared table columns, reused by this resource's own table, the
     * RegistrationsRelationManager on CampResource, and the embedded table
     * on the ZoneCamps dashboard page — one definition, no drift.
     */
    public static function getTableColumns(): array
    {
        return [
            Tables\Columns\ImageColumn::make('photo')
                ->label('')
                ->circular(),
            Tables\Columns\TextColumn::make('nom')
                ->label('Nom')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('campCategory.name')
                ->label('Rôle')
                ->badge()
                ->color('gray'),
            Tables\Columns\TextColumn::make('sexe')
                ->label('Sexe')
                ->badge(),
            Tables\Columns\TextColumn::make('date_naissance')
                ->label('Naissance')
                ->date('d/m/Y')
                ->sortable(),
            Tables\Columns\TextColumn::make('camp.name')
                ->label('Camp')
                ->badge()
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('telephone')
                ->label('Téléphone')
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('nif_cin')
                ->label('Nif / Cin')
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('arrondissement.name')
                ->label('Arrondissement')
                ->toggleable(),
            Tables\Columns\TextColumn::make('club.name')
                ->label('Club')
                ->toggleable(),
            Tables\Columns\TextColumn::make('role_responsable')
                ->label('Type de responsable')
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('campus')
                ->label('Campus')
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('type_camp')
                ->label('Type de Camps')
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('statut')
                ->label('Statut')
                ->badge(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(static::getTableColumns())
            ->filters([
                Tables\Filters\SelectFilter::make('camp_id')
                    ->label('Camp')
                    ->relationship('camp', 'name')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('statut')
                    ->label('Statut')
                    ->options(RegistrationStatut::class),
                Tables\Filters\SelectFilter::make('sexe')
                    ->label('Sexe')
                    ->options(Sexe::class),
            ])
            ->actions([
                Tables\Actions\Action::make('print_card')
                    ->label('Imprimer la fiche')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->action(fn (Registration $record) => static::exportCardPdf($record)),
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

    public static function exportCardPdf(Registration $registration): StreamedResponse
    {
        $registration->load(['camp', 'club', 'arrondissement', 'campCategory']);

        $pdf = Pdf::loadView('pdf.registration-card', [
            'registration' => $registration,
        ])->setPaper('a5', 'portrait');

        $filename = 'fiche-' . Str::slug($registration->nom) . '.pdf';

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf'],
        );
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
            'create' => Pages\CreateRegistration::route('/create'),
            'edit' => Pages\EditRegistration::route('/{record}/edit'),
        ];
    }
}
