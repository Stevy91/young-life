<div class="space-y-4">
    <div class="flex flex-wrap gap-2">
        @forelse ($arrondissements as $arrondissement)
            <x-filament::button
                tag="a"
                :href="route('camps.print-list', ['camp' => $camp, 'arrondissement' => $arrondissement->id])"
                target="_blank"
                color="gray"
                icon="heroicon-o-printer"
            >
                {{ $arrondissement->name }}
            </x-filament::button>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Aucun participant inscrit pour l'instant dans ce camp.
            </p>
        @endforelse
    </div>

    <div class="flex justify-end border-t border-gray-200 pt-4 dark:border-white/10">
        <x-filament::button
            tag="a"
            :href="route('camps.print-list', ['camp' => $camp])"
            target="_blank"
            color="danger"
            icon="heroicon-o-printer"
        >
            Imprimer la liste complète
        </x-filament::button>
    </div>
</div>
