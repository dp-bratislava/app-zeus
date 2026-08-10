<x-filament-panels::page class="assets-photo-page">

    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-wrap items-center gap-2 border-b border-gray-100 px-4 pt-2 dark:border-gray-800">
            <button
                type="button"
                wire:click="$set('findMode', 'recent')"
                class="flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-semibold transition
                       {{ $this->findMode === 'recent'
                           ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                           : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="h-4 w-4" />
                Nedávne demontáže
            </button>

            <button
                type="button"
                wire:click="useVehicleForm"
                class="flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-semibold transition
                       {{ $this->findMode === 'vehicle'
                           ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                           : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                <x-filament::icon icon="heroicon-o-truck" class="h-4 w-4" />
                Podľa vozidla
            </button>
        </div>

        <div class="p-4 sm:p-6">
            @if ($this->findMode === 'vehicle')
                {{ $this->form }}

                @if (! empty($data['asset_movement_id'] ?? null))
                    <div class="mt-4">
                        <x-filament::button
                            size="lg"
                            wire:click="openMovementPhotos({{ $data['asset_movement_id'] }})"
                            class="!rounded-xl shadow-lg shadow-primary-500/20 sm:!px-10">
                            Nahrať fotky
                        </x-filament::button>
                    </div>
                @endif
            @else
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @forelse ($this->recentDemontazes as $demontaz)
                        <button
                            type="button"
                            wire:click="openMovementPhotos({{ $demontaz['id'] }})"
                            title="Nahratať / spravovať fotky"
                            class="group rounded-xl border border-gray-200 p-3 text-left transition
                                   {{ $demontaz['photo_count'] > 0
                                       ? 'bg-orange-50/70 hover:bg-orange-100 dark:bg-orange-500/10 dark:hover:bg-orange-500/15'
                                       : 'bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-800/60' }}">
                            <div class="flex items-center justify-between gap-2">
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ 'Vozidlo: '. ($demontaz['vehicle_label'] ?? 'N/A') . ',' . ($demontaz['slot_label'] ? ' ' . $demontaz['slot_label'] : '') }}
                                </p>
                                <span class="inline-flex shrink-0 items-center gap-1 rounded-full px-1.5 py-0.5 text-xs font-semibold
                                             {{ $demontaz['photo_count'] > 0
                                                 ? 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400'
                                                 : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                    <x-filament::icon
                                        icon="heroicon-o-photo"
                                        class="h-3.5 w-3.5" />
                                    {{ $demontaz['photo_count'] }} {{ $demontaz['photo_count'] === 1 ? 'fotka' : ($demontaz['photo_count'] >= 2 && $demontaz['photo_count'] <= 4 ? 'fotky' : 'fotiek') }}
                                </span>
                            </div>

                            @if ($demontaz['vehicle_model'])
                                <p class="mt-0.5 truncate text-xs text-gray-500 dark:text-gray-400">
                                    {{ $demontaz['vehicle_model'] }}
                                </p>
                            @endif

                            <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                @if ($demontaz['date'])
                                    <span class="inline-flex items-center gap-1">
                                        <x-filament::icon icon="heroicon-o-calendar" class="h-3.5 w-3.5" />
                                        {{ $demontaz['date'] }}
                                    </span>
                                @endif
                                @if ($demontaz['task_item_group'])
                                    <span class="inline-flex items-center gap-1">
                                        <x-filament::icon icon="heroicon-o-tag" class="h-3.5 w-3.5" />
                                        {{ $demontaz['task_item_group'] }}
                                    </span>
                                @endif
                                <span class="text-gray-400 dark:text-gray-500">{{ $demontaz['created_at']?->diffForHumans() }}</span>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full px-4 py-10 text-center">
                            <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="mx-auto mb-2 h-8 w-8 text-gray-400" />
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Žiadne nedávne demontáže</p>
                        </div>
                    @endforelse
                </div>
            @endif
        </div>
    </section>

    <style>
        .assets-photo-page { --fi-avatar-size: 2.25rem; }
        .assets-photo-page .fi-section { overflow: visible; }
        .assets-photo-page .fi-fo-grid-cols-2 { grid-template-columns: 1fr !important; row-gap: 1rem; }
        .assets-photo-page .fi-section-content-ctn { padding-inline: 0.25rem; }
        @media (min-width: 640px) {
            .assets-photo-page .fi-fo-grid-cols-2 { grid-template-columns: repeat(2, minmax(0,1fr)) !important; }
        }
    </style>

</x-filament-panels::page>