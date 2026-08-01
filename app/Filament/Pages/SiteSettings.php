<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    /**
     * Title shown in the panel header (see AdminPanelProvider::brandName())
     * until a Super Admin changes it here — no code deploy needed either way.
     */
    public const SITE_TITLE_KEY = 'site_title';

    public const SITE_TITLE_DEFAULT = 'Participants Camps YoungLife 2026';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Paramètres';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    /**
     * Guards against the panel booting before migrations have run (e.g. a
     * fresh test database), same pattern as AdminPanelProvider's zone nav.
     */
    public static function currentTitle(): string
    {
        return Schema::hasTable('settings')
            ? Setting::get(self::SITE_TITLE_KEY, self::SITE_TITLE_DEFAULT)
            : self::SITE_TITLE_DEFAULT;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'site_title' => Setting::get(self::SITE_TITLE_KEY, self::SITE_TITLE_DEFAULT),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                TextInput::make('site_title')
                    ->label("Titre affiché dans l'en-tête")
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set(self::SITE_TITLE_KEY, $data['site_title']);

        Notification::make()
            ->title('Paramètres enregistrés')
            ->success()
            ->send();
    }
}
