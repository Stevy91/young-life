<?php

namespace App\Filament\Pages;

use App\Enums\CampStatus;
use App\Filament\Resources\CampResource;
use App\Filament\Resources\RegistrationResource;
use App\Models\Arrondissement;
use App\Models\Camp;
use App\Models\Registration;
use App\Models\Zone;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class ZoneCamps extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $slug = 'zones/{zone}/camps';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.zone-camps';

    public Zone $zone;

    public ?int $selectedCampId = null;

    public bool $showHidden = false;

    public function mount(Zone $zone): void
    {
        $user = Auth::user();

        abort_unless(
            $user->isSuperAdmin() || $user->zones()->whereKey($zone->id)->exists(),
            403,
        );

        $this->zone = $zone;
        $this->selectedCampId = $this->getCamps()->first()?->id;
    }

    /**
     * Filament Shield instantiates every panel page outside its normal
     * mount() lifecycle (e.g. to label pages on the Rôles create/edit
     * screens), so $zone is sometimes unset here — guard instead of
     * crashing with "typed property must not be accessed before
     * initialization".
     */
    public function getTitle(): string
    {
        return isset($this->zone) ? $this->zone->name : 'Camps';
    }

    /**
     * Camps recur every year under the same name (Konbit I, Konbit II...);
     * once a season ends it gets marked Archivé so it stops cluttering this
     * dashboard, but its data and history stay reachable via the toggle.
     * Brouillon is hidden the same way: a camp that got fully set up but
     * ended up not happening this time can be tucked away without being
     * deleted, and reopened later by switching it back to Ouvert.
     */
    public function getCamps(): Collection
    {
        return $this->zone->camps()
            ->withCount('registrations')
            ->when(
                ! $this->showHidden,
                fn (Builder $query) => $query->whereNotIn('statut', [
                    CampStatus::Archive->value,
                    CampStatus::Brouillon->value,
                ]),
            )
            ->orderBy('name')
            ->get();
    }

    public function toggleShowHidden(): void
    {
        $this->showHidden = ! $this->showHidden;
    }

    /**
     * Fast season rollover: same name/zone/roles+quotas as $campId, but
     * blank dates and zero registrations — the old camp is never touched.
     */
    public function duplicateCamp(int $campId): void
    {
        abort_unless(Auth::user()->can('create', Camp::class), 403);

        $new = CampResource::duplicate(Camp::findOrFail($campId));

        $this->redirect(CampResource::getUrl('edit', ['record' => $new]));
    }

    public function toggleArchiveCamp(int $campId): void
    {
        $camp = Camp::findOrFail($campId);

        abort_unless(Auth::user()->can('update', $camp), 403);

        CampResource::toggleArchive($camp);
    }

    public function getSelectedCamp(): ?Camp
    {
        if (! $this->selectedCampId) {
            return null;
        }

        return $this->getCamps()->firstWhere('id', $this->selectedCampId);
    }

    public function selectCamp(int $campId): void
    {
        $this->selectedCampId = $campId;
        $this->resetTable();
    }

    /**
     * Null while the selected camp is Ouvert (nothing to warn about).
     * Otherwise a short French sentence naming its actual status, reused as
     * the basis for every "this camp isn't open" popup below.
     */
    private function campStatusWarning(): ?string
    {
        $camp = $this->getSelectedCamp();

        if (! $camp || $camp->statut === CampStatus::Ouvert) {
            return null;
        }

        return "Ce camp est actuellement « {$camp->statut->getLabel()} ».";
    }

    /**
     * Super Admin keeps the same override used for new registrations
     * (RegistrationResource::campAcceptsNewRegistrations) — everyone else is
     * fully blocked from editing or deleting a participant once the camp
     * isn't Ouvert, not just warned.
     */
    private function isEditOrDeleteBlocked(): bool
    {
        return (bool) $this->campStatusWarning() && ! Auth::user()->isSuperAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Registration::query()->where('camp_id', $this->selectedCampId ?? 0))
            ->columns(RegistrationResource::getTableColumns())
            ->filters([
                Tables\Filters\SelectFilter::make('camp_category_id')
                    ->label('Rôle')
                    ->relationship('campCategory', 'name'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('print_list')
                    ->label('Imprimer la liste')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->modalHeading('Imprimer la liste des participants')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer')
                    ->modalContent(function () {
                        $camp = $this->getSelectedCamp();

                        $arrondissements = $camp
                            ? Arrondissement::whereHas(
                                'registrations',
                                fn (Builder $query) => $query->where('camp_id', $camp->id),
                            )->orderBy('name')->get()
                            : collect();

                        return view('filament.pages.partials.print-list-modal', [
                            'camp' => $camp,
                            'arrondissements' => $arrondissements,
                        ]);
                    }),
                Tables\Actions\Action::make('add_registration')
                    ->label('Ajouter un participant')
                    ->icon('heroicon-o-user-plus')
                    // A plain Action (unlike CreateAction) doesn't
                    // auto-check the Registration policy, so "Lecteur"
                    // (read-only) needs this checked explicitly.
                    ->visible(fn () => Auth::user()->can('create', Registration::class))
                    ->modalHeading(fn () => $this->campStatusWarning() ? 'Camp non ouvert aux inscriptions' : 'Ajouter un participant')
                    ->modalDescription(function () {
                        $warning = $this->campStatusWarning();

                        if (! $warning) {
                            return null;
                        }

                        return Auth::user()->isSuperAdmin()
                            ? "{$warning} En tant que Super Admin, vous pouvez tout de même ajouter un participant."
                            : "{$warning} Aucune nouvelle inscription n'est possible tant qu'il n'est pas repassé à Ouvert.";
                    })
                    ->modalIcon(fn () => $this->campStatusWarning() ? 'heroicon-o-exclamation-triangle' : null)
                    ->modalIconColor(fn () => $this->campStatusWarning() ? 'warning' : null)
                    // The form itself is unchanged — the Rôle field already
                    // disables every option and rejects submission via its
                    // own validation rule (see RegistrationResource) when the
                    // camp isn't Ouvert. This just adds the warning banner
                    // above it explaining why, instead of a bare disabled
                    // dropdown with no context.
                    ->form(fn () => RegistrationResource::getFormSchema($this->getSelectedCamp()))
                    ->action(function (array $data): void {
                        $data['camp_id'] = $this->selectedCampId;
                        Registration::create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('print_card')
                    ->label('Imprimer')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->action(fn (Registration $record) => RegistrationResource::exportCardPdf($record)),
                // EditAction/DeleteAction don't auto-check the model policy
                // outside a Filament Resource's own table, so "Lecteur"
                // (read-only) needs this checked explicitly.
                //
                // Both are fully blocked on a non-Ouvert camp — a popup
                // explains why instead of silently doing nothing. Super Admin
                // keeps the same override used everywhere else in this
                // feature (see RegistrationResource::campAcceptsNewRegistrations).
                Tables\Actions\EditAction::make()
                    ->visible(fn (Registration $record) => Auth::user()->can('update', $record))
                    ->form(fn () => $this->isEditOrDeleteBlocked() ? [] : RegistrationResource::getFormSchema($this->getSelectedCamp()))
                    ->modalHeading(fn () => $this->isEditOrDeleteBlocked() ? 'Camp non ouvert' : null)
                    ->modalDescription(function () {
                        $warning = $this->campStatusWarning();

                        if (! $warning) {
                            return null;
                        }

                        return Auth::user()->isSuperAdmin()
                            ? "{$warning} En tant que Super Admin, vous pouvez tout de même modifier ce participant."
                            : "{$warning} Impossible de modifier ce participant tant qu'il n'est pas réouvert.";
                    })
                    ->modalIcon(fn () => $this->isEditOrDeleteBlocked() ? 'heroicon-o-exclamation-triangle' : null)
                    ->modalIconColor(fn () => $this->isEditOrDeleteBlocked() ? 'warning' : null)
                    ->modalSubmitAction(fn ($action) => $this->isEditOrDeleteBlocked() ? false : $action)
                    ->action(function (Registration $record, array $data): void {
                        // Belt and suspenders: the empty form + hidden submit
                        // button above are UX only, this is the real guard.
                        if ($this->isEditOrDeleteBlocked()) {
                            return;
                        }

                        $record->update($data);

                        Notification::make()->title('Sauvegardé(e)')->success()->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Registration $record) => Auth::user()->can('delete', $record))
                    ->modalHeading(fn () => $this->isEditOrDeleteBlocked() ? 'Camp non ouvert' : null)
                    ->modalDescription(function () {
                        $warning = $this->campStatusWarning();

                        if (! $warning) {
                            return null;
                        }

                        return Auth::user()->isSuperAdmin()
                            ? "{$warning} En tant que Super Admin, vous pouvez tout de même supprimer ce participant."
                            : "{$warning} Impossible de supprimer ce participant tant qu'il n'est pas réouvert.";
                    })
                    ->modalSubmitAction(fn ($action) => $this->isEditOrDeleteBlocked() ? false : $action)
                    ->action(function (Registration $record): void {
                        if ($this->isEditOrDeleteBlocked()) {
                            return;
                        }

                        $record->delete();

                        Notification::make()->title('Supprimé(e)')->success()->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()->can('deleteAny', Registration::class)),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Aucun participant pour ce camp pour l\'instant.');
    }
}
