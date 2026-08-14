<x-filament-panels::page class="assets-photo-page">

    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4 dark:border-gray-800 overflow-x-auto">
            <x-filament::button
                wire:click="$set('findMode', 'recent')"
                :color="$this->findMode === 'recent' ? 'primary' : 'gray'"
                icon="heroicon-o-wrench-screwdriver"
                :outlined="$this->findMode !== 'recent'"
                class="whitespace-nowrap">
                Fotky - demontáže
            </x-filament::button>

            <x-filament::button
                wire:click="useTaskForm"
                :color="$this->findMode === 'task' ? 'primary' : 'gray'"
                icon="fas-bus"
                :outlined="$this->findMode !== 'task'"
                class="whitespace-nowrap">
                Fotky Havárie
            </x-filament::button>
        </div>

        <div class="p-6 sm:p-8">
            @if ($this->findMode === 'task')
                {{ $this->taskForm }}

                @if (! empty($taskData['task_id'] ?? null))
                    <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-3">
                            <span class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                <x-filament::icon icon="fas-bus" class="h-5 w-5 text-gray-400" />
                                Vozidlo:
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $this->selectedTaskInfo['vehicle_label'] }}</span>
                            </span>
                            <span class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                <x-filament::icon icon="heroicon-o-cog-6-tooth" class="h-5 w-5 text-gray-400" />
                                Model:
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $this->selectedTaskInfo['vehicle_model'] }}</span>
                            </span>
                            <span class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                <x-filament::icon icon="heroicon-o-tag" class="h-5 w-5 text-gray-400" />
                                <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-700 dark:bg-red-500/15 dark:text-red-400">
                                    {{ $this->selectedTaskInfo['group_title'] }}
                                </span>
                            </span>
                        </div>
                    </div>

                    @if ($this->selectedTaskItems->isEmpty())
                        <div class="col-span-full px-6 py-16 text-center">
                            <x-filament::icon icon="heroicon-o-arrow-path" class="mx-auto mb-4 h-12 w-12 text-gray-400" />
                            <p class="text-lg font-semibold text-gray-600 dark:text-gray-300">Žiadne podzákazky pre túto zákazku</p>
                        </div>
                    @else
                        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($this->selectedTaskItems as $item)
                                <div class="flex flex-col rounded-xl border-2 border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                                    <div class="flex items-start justify-between gap-3">
                                        <p class="truncate text-base font-bold text-gray-900 dark:text-white">
                                            {{ $item['group_title'] ?: ('Podzákazka #' . $item['id']) }}
                                        </p>
                                        <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold
                                                     {{ $item['photo_count'] > 0
                                                         ? 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400'
                                                         : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                            <x-filament::icon icon="heroicon-o-photo" class="h-4 w-4" />
                                            {{ $item['photo_count'] }}
                                        </span>
                                    </div>

                                    @if ($item['photos']->isNotEmpty())
                                        <div class="mt-3 grid grid-cols-3 gap-2">
                                            @foreach ($item['photos'] as $photo)
                                                <a
                                                    href="{{ $photo['url'] }}"
                                                    target="_blank"
                                                    title="{{ $photo['name'] }}"
                                                    class="group relative block aspect-square overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700">
                                                    <img
                                                        src="{{ $photo['url'] }}"
                                                        alt="{{ $photo['name'] }}"
                                                        loading="lazy"
                                                        class="h-full w-full object-cover transition group-hover:scale-105" />
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="mt-3 flex items-center justify-center rounded-lg border border-dashed border-gray-300 py-8 text-gray-400 dark:border-gray-600">
                                            <span class="text-sm">Žiadne fotky</span>
                                        </div>
                                    @endif

                                    <div class="mt-auto pt-4">
                                        <x-filament::button
                                            size="sm"
                                            wire:click="openTaskItemPhotos({{ $item['id'] }})"
                                            icon="heroicon-m-photo"
                                            class="w-full">
                                            Nahrať fotky
                                        </x-filament::button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @forelse ($this->recentDemontazes as $demontaz)
                        <button
                            type="button"
                            wire:click="openMovementPhotos({{ $demontaz['id'] }})"
                            title="Nahrať / spravovať fotky"
                            class="group rounded-xl border-2 border-gray-200 p-5 text-left transition hover:shadow-md
                                   {{ $demontaz['photo_count'] > 0
                                       ? 'bg-orange-50/70 hover:bg-orange-100 dark:bg-orange-500/10 dark:hover:bg-orange-500/15'
                                       : 'bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-800/60' }}">
                            <div class="flex items-center justify-between gap-3">
                                <p class="truncate text-base font-bold text-gray-900 dark:text-white">
                                    {{ ($demontaz['vehicle_label'] ?? 'N/A') . ',' . ($demontaz['slot_label'] ? ' ' . $demontaz['slot_label'] : '') }}
                                </p>
                                <span class="inline-flex shrink-0 items-center gap-2 rounded-full px-3 py-2 text-sm font-bold
                                             {{ $demontaz['photo_count'] > 0
                                                 ? 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400'
                                                 : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                    <x-filament::icon
                                        icon="heroicon-o-photo"
                                        class="h-5 w-5" />
                                    {{ $demontaz['photo_count'] }} {{ $demontaz['photo_count'] === 1 ? 'fotka' : ($demontaz['photo_count'] >= 2 && $demontaz['photo_count'] <= 4 ? 'fotky' : 'fotiek') }}
                                </span>
                            </div>

                            @if ($demontaz['vehicle_model'])
                                <p class="mt-2 truncate text-sm text-gray-600 dark:text-gray-300">
                                    {{ $demontaz['vehicle_model'] }}
                                </p>
                            @endif

                            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-600 dark:text-gray-400">
                                @if ($demontaz['date'])
                                    <span class="inline-flex items-center gap-2">
                                        <x-filament::icon icon="heroicon-o-calendar" class="h-4 w-4" />
                                        {{ $demontaz['date'] }}
                                    </span>
                                @endif
                                @if ($demontaz['task_item_group'])
                                    <span class="inline-flex items-center gap-2">
                                        <x-filament::icon icon="heroicon-o-tag" class="h-4 w-4" />
                                        {{ $demontaz['task_item_group'] }}
                                    </span>
                                @endif
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full px-6 py-16 text-center">
                            <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="mx-auto mb-4 h-12 w-12 text-gray-400" />
                            <p class="text-lg font-semibold text-gray-600 dark:text-gray-300">Žiadne nedávne demontáže</p>
                        </div>
                    @endforelse
                </div>
            @endif
        </div>
    </section>

    <style>
        .assets-photo-page { --fi-avatar-size: 2.75rem; }
        .assets-photo-page .fi-section { overflow: visible; }
        .assets-photo-page .fi-fo-grid-cols-2 { grid-template-columns: 1fr !important; row-gap: 1.5rem; }
        .assets-photo-page .fi-section-content-ctn { padding-inline: 0.5rem; }
        @media (min-width: 640px) {
            .assets-photo-page .fi-fo-grid-cols-2 { grid-template-columns: repeat(2, minmax(0,1fr)) !important; }
        }
    </style>

</x-filament-panels::page>