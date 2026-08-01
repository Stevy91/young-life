<?php

namespace App\Filament\Resources\CampResource\RelationManagers;

use App\Enums\RegistrationStatut;
use App\Enums\Sexe;
use App\Filament\Resources\RegistrationResource;
use App\Models\Registration;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Inscriptions';

    protected static ?string $modelLabel = 'inscription';

    public function form(Form $form): Form
    {
        // Same fields as the standalone RegistrationResource, minus the camp
        // picker (implicit here: we're already inside a camp's page) — the
        // owning Camp is passed through so the category/arrondissement
        // options are scoped to it from the start.
        return $form->schema(RegistrationResource::getFormSchema($this->getOwnerRecord()));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nom')
            ->columns(RegistrationResource::getTableColumns())
            ->filters([
                Tables\Filters\SelectFilter::make('statut')
                    ->label('Statut')
                    ->options(RegistrationStatut::class),
                Tables\Filters\SelectFilter::make('sexe')
                    ->label('Sexe')
                    ->options(Sexe::class),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('print_card')
                    ->label('Imprimer la fiche')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->action(fn (Registration $record) => RegistrationResource::exportCardPdf($record)),
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
