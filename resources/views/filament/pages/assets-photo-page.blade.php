{{-- resources/views/filament/pages/assets-photo-page.blade.php --}}
<x-filament-panels::page class="assets-photo-page">

    {{-- Step 1: choose how to find the movement, then pick it --}}
    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">

        <div class="flex flex-wrap items-center gap-2 border-b border-gray-100 px-4 pt-2 dark:border-gray-800">
            {{-- Tab 1: by vehicle / task --}}
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

            {{-- Tab 2: recent demontáže --}}
            <button
                type="button"
                wire:click="$set('findMode', 'recent')"
                class="flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-semibold transition
                       {{ $this->findMode === 'recent'
                            ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                            : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="h-4 w-4" />
                Nedávne demontáže
                <span class="rounded-full bg-primary-50 px-1.5 py-0.5 text-xs font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">
                    {{ count($this->recentDemontazes) }}
                </span>
            </button>
        </div>

        <div class="p-4 sm:p-6">
            @if ($this->findMode === 'vehicle')
                    {{ $this->form }}

                @if (! empty($data['asset_movement_id'] ?? null))
                    <div class="mt-5">
                        <x-filament::button
                            size="lg"
                            wire:click="openMovementPhotos({{ $data['asset_movement_id'] }})"
                            class="!rounded-xl shadow-lg shadow-primary-500/20 sm:!px-10">
                            Upload Photos
                        </x-filament::button>
                    </div>
                @endif
            @else
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @forelse ($this->recentDemontazes as $demontaz)
                        <button
                            type="button"
                            wire:click="openMovementPhotos({{ $demontaz['id'] }})"
                            title="Upload / manage photos"
                            class="group rounded-xl border border-gray-200 p-3 text-left transition
                                   {{ $demontaz['photo_count'] > 0
                                        ? 'bg-emerald-50/70 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/15'
                                        : 'bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-800/60' }}">
                            <div class="flex items-center justify-between gap-2">
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $demontaz['vehicle_label'] ?? '#' . $demontaz['id'] }}
                                </p>
                                <span class="inline-flex shrink-0 items-center gap-1 rounded-full px-1.5 py-0.5 text-xs font-semibold
                                             {{ $demontaz['photo_count'] > 0
                                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400'
                                                : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                    <x-filament::icon
                                        icon="{{ $demontaz['photo_count'] > 0 ? 'heroicon-o-photo' : 'heroicon-o-arrow-up-tray' }}"
                                        class="h-3.5 w-3.5" />
                                    {{ $demontaz['photo_count'] > 0 ? $demontaz['photo_count'] . ' photos' : 'Add photos' }}
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
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">No recent disassemblies</p>
                        </div>
                    @endforelse
                </div>
            @endif
        </div>
    </section>

            {{-- Photo history grouped per movement --}}
    <section class="mt-8">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <h2 class="flex items-center gap-2 text-lg font-bold tracking-tight text-gray-900 dark:text-white">
                <x-filament::icon icon="heroicon-o-photo" class="h-5 w-5 text-primary-500" />
                Photo History
            </h2>
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                {{ count($this->history) }} {{ Str::plural('movement', count($this->history)) }} with photos
            </span>
        </div>

        @forelse ($this->history as $movement)
            <div class="mb-4 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 {{ ($data['asset_movement_id'] ?? null) == $movement['id'] ? 'ring-2 ring-primary-500' : '' }}">
                <div class="flex flex-col gap-2 border-b border-gray-100 px-4 py-3 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 flex-col gap-1">
                        <span class="truncate text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $movement['vehicle_label'] ?? $movement['task_item_title'] ?? 'Movement #'.$movement['id'] }}
                        </span>
                        @if ($movement['vehicle_label'] && $movement['task_item_title'])
                            <span class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $movement['task_item_title'] }}</span>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs">
                        @if ($movement['date'])
                            <span class="inline-flex items-center gap-1 text-gray-500 dark:text-gray-400">
                                <x-filament::icon icon="heroicon-o-calendar" class="h-3.5 w-3.5" />
                                {{ $movement['date'] }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1 font-medium text-gray-500 dark:text-gray-400">
                            <x-filament::icon icon="heroicon-o-document" class="h-3.5 w-3.5" />
                            {{ $movement['label'] }}
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-primary-50 px-2 py-0.5 font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">
                            <x-filament::icon icon="heroicon-o-photo" class="h-3.5 w-3.5" />
                            {{ count($movement['photos']) }}
                        </span>
                        <span class="text-gray-400 dark:text-gray-500">&middot; {{ $movement['updated_at']?->diffForHumans() }}</span>
                    </div>
                </div>

                @if (count($movement['photos']))
                    <div class="grid grid-cols-2 gap-2 p-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                        @foreach ($movement['photos'] as $media)
                            <a href="{{ $media['url'] }}" target="_blank" rel="noopener"
                               class="group block aspect-square overflow-hidden rounded-xl border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-800">
                                <img src="{{ $media['url'] }}" alt="Asset photo" loading="lazy"
                                     class="h-full w-full object-cover transition duration-200 group-hover:scale-105">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center dark:border-gray-700 dark:bg-gray-900">
                <x-filament::icon icon="heroicon-o-photo" class="mx-auto mb-3 h-10 w-10 text-gray-400" />
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">No photo history yet</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Select a vehicle, task item and movement, then upload photos to see them here.
                </p>
            </div>
        @endforelse
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