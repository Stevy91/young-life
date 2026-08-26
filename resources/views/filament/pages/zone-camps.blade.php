<x-filament-panels::page>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="fi-section-header-description text-sm text-gray-500 dark:text-gray-400">
            {{ $this->getCamps()->count() }} camp(s) dans cette zone.
        </p>

        <div class="flex items-center gap-2">
            <x-filament::button
                icon="heroicon-o-archive-box"
                wire:click="toggleShowHidden"
                color="gray"
                outlined
            >
                {{ $showHidden ? 'Masquer brouillons et archivés' : 'Afficher brouillons et archivés' }}
            </x-filament::button>

            @can('create', \App\Models\Camp::class)
                <x-filament::button
                    icon="heroicon-o-plus-circle"
                    :href="\App\Filament\Resources\CampResource::getUrl('create', ['zone_id' => $zone->id])"
                    tag="a"
                    color="gray"
                >
                    Nouveau camp
                </x-filament::button>
            @endcan
        </div>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        @forelse ($this->getCamps() as $camp)
            <button
                type="button"
                wire:click="selectCamp({{ $camp->id }})"
                @class([
                    'flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition whitespace-nowrap',
                    'bg-primary-600 text-white shadow' => $selectedCampId === $camp->id,
                    'bg-white text-gray-700 ring-1 ring-gray-950/10 hover:bg-gray-50 dark:bg-white/5 dark:text-gray-200 dark:ring-white/10 dark:hover:bg-white/10' => $selectedCampId !== $camp->id,
                ])
            >
                {{ $camp->name }}
                @if ($camp->date_debut)
                    <span class="opacity-70">· {{ $camp->date_debut->format('Y') }}</span>
                @endif
                @if ($camp->statut === \App\Enums\CampStatus::Archive)
                    <x-filament::icon icon="heroicon-o-archive-box" class="h-3.5 w-3.5 opacity-70" />
                @endif
                <span @class([
                    'rounded-full px-1.5 py-0.5 text-xs',
                    'bg-white/20' => $selectedCampId === $camp->id,
                    'bg-gray-100 dark:bg-white/10' => $selectedCampId !== $camp->id,
                ])>{{ $camp->registrations_count }}</span>
            </button>
        @empty
            <x-filament::section>
                Aucun camp pour l'instant dans cette zone.
            </x-filament::section>
        @endforelse
    </div>

    @if ($camp = $this->getSelectedCamp())
        <div class="mt-6 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-gray-950 dark:text-white">{{ $camp->name }}</h2>
                    @if ($camp->date_debut)
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $camp->date_debut->format('Y') }}</span>
                    @endif
                    <x-filament::badge :color="$camp->statut->getColor()">
                        {{ $camp->statut->getLabel() }}
                    </x-filament::badge>
                </div>

                <div class="flex items-center gap-3">
                    @can('create', \App\Models\Camp::class)
                        <button
                            type="button"
                            wire:click="duplicateCamp({{ $camp->id }})"
                            wire:confirm="Créer une copie de « {{ $camp->name }} » (mêmes rôles/quotas, dates à définir, sans les inscrits) ?"
                            class="fi-link inline-flex items-center gap-1 text-sm font-semibold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        >
                            Dupliquer &rarr;
                        </button>
                    @endcan

                    @can('update', $camp)
                        <button
                            type="button"
                            wire:click="toggleArchiveCamp({{ $camp->id }})"
                            @if ($camp->statut === \App\Enums\CampStatus::Archive)
                                wire:confirm="Désarchiver « {{ $camp->name }} » et le remettre au statut Ouvert ?"
                            @else
                                wire:confirm="Archiver « {{ $camp->name }} » ? Il disparaîtra du tableau de bord par défaut (récupérable via « Afficher les camps archivés »)."
                            @endif
                            class="fi-link inline-flex items-center gap-1 text-sm font-semibold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        >
                            {{ $camp->statut === \App\Enums\CampStatus::Archive ? 'Désarchiver' : 'Archiver' }} &rarr;
                        </button>

                        <x-filament::link :href="\App\Filament\Resources\CampResource::getUrl('edit', ['record' => $camp])">
                            Modifier les informations du camp &rarr;
                        </x-filament::link>
                    @endcan
                </div>
            </div>

            @php $categories = $camp->categories()->withCount('registrations')->get(); @endphp
            @if ($categories->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach ($categories as $category)
                        <x-filament::badge :color="$category->quota !== null && $category->registrations_count >= $category->quota ? 'danger' : 'success'">
                            {{ $category->name }} :
                            {{ $category->registrations_count }}{{ $category->quota !== null ? " sur {$category->quota}" : '' }}
                        </x-filament::badge>
                    @endforeach
                </div>
            @endif

            {{ $this->table }}
        </div>
    @endif
</x-filament-panels::page>
