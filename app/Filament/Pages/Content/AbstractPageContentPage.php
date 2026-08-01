<?php

namespace App\Filament\Pages\Content;

use App\Models\PageContent;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

/**
 * Shared mount()/save()/access-control for the 4 "Pages du site" content
 * editors — each subclass only needs to declare pageKey() and its own
 * form() schema (field names must match what the public Blade view reads
 * from PageContent::forPage($pageKey)->data).
 */
abstract class AbstractPageContentPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Pages du site';

    protected static string $view = 'filament.pages.content.form-page';

    public ?array $data = [];

    abstract public static function pageKey(): string;

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
        $this->form->fill(PageContent::forPage(static::pageKey())->data);
    }

    public function save(): void
    {
        PageContent::forPage(static::pageKey())->update([
            'data' => $this->form->getState(),
        ]);

        Notification::make()
            ->title('Page mise à jour')
            ->success()
            ->send();
    }
}
