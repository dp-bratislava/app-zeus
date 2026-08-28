<x-filament-panels::page class="assets-photo-page">

    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        
        {{-- Navigation Tabs --}}
        <div class="flex items-center gap-3 overflow-x-auto border-b border-gray-100 px-6 py-4">
            <x-filament::button
                wire:click="showAgregaty"
                :color="$this->findMode === 'agregaty' ? 'primary' : 'gray'"
                icon="heroicon-o-wrench-screwdriver"
                :outlined="$this->findMode !== 'agregaty'"
                class="whitespace-nowrap">
                Fotky - agregáty
            </x-filament::button>

            <x-filament::button
                wire:click="showAccidents"
                :color="in_array($this->findMode, ['task', 'accidents']) ? 'primary' : 'gray'"
                icon="fas-bus"
                :outlined="!in_array($this->findMode, ['task', 'accidents'])"
                class="whitespace-nowrap">
                Fotky Havárie
            </x-filament::button>

            <x-filament::button
                wire:click="showBuffer"
                :color="$this->findMode === 'buffer' ? 'primary' : 'gray'"
                icon="heroicon-o-photo"
                :outlined="$this->findMode !== 'buffer'"
                class="whitespace-nowrap">
                Fotosérie
            </x-filament::button>
        </div>

        <div class="p-6 sm:p-8">

            {{-- TAB 1: Agregáty --}}
            @if ($this->findMode === 'agregaty')
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @forelse ($this->demontazes as $demontaz)
                        <button
                            type="button"
                            wire:click="openMovementPhotos({{ $demontaz['id'] }})"
                            title="Nahrať / spravovať fotky"
                            class="group rounded-xl border-2 border-gray-200 p-5 text-left transition hover:shadow-md {{ $demontaz['photo_count'] > 0 ? 'bg-orange-50/70 hover:bg-orange-100' : 'bg-white hover:bg-gray-50' }}">
                            
                            <div class="flex items-center justify-between gap-3">
                                <p class="truncate text-base font-bold text-gray-900">
                                    {{ ($demontaz['vehicle_label'] ?? 'N/A') . ($demontaz['slot_label'] ? ', ' . $demontaz['slot_label'] : '') }}
                                </p>

                                <span class="inline-flex shrink-0 items-center gap-2 rounded-full px-3 py-2 text-sm font-bold {{ $demontaz['photo_count'] > 0 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600' }}">
                                    <x-filament::icon icon="heroicon-o-photo" class="h-5 w-5" />
                                    {{ $demontaz['photo_count'] }} {{ $demontaz['photo_count'] === 1 ? 'fotka' : ($demontaz['photo_count'] >= 2 && $demontaz['photo_count'] <= 4 ? 'fotky' : 'fotiek') }}
                                </span>
                            </div>
                            @if ($demontaz['serial_number'])
                                <p @class([
                                    'mt-2 truncate text-sm',
                                    'text-red-600' => $demontaz['is_serial_number_virtual'] === true,
                                    'text-gray-600' => $demontaz['is_serial_number_virtual'] === false,
                                ])>
                                    {{ $demontaz['serial_number'] ?? 'N/A' }}
                                </p>
                            @endif

                            @if ($demontaz['vehicle_model'])
                                <p class="mt-2 truncate text-sm text-gray-600">
                                    {{ $demontaz['vehicle_model'] }}
                                </p>
                            @endif

                            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-600">
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

                                <span class="inline-flex items-center gap-2">
                                    <x-filament::icon icon="heroicon-o-cog-6-tooth" class="h-4 w-4" />
                                    demontáž
                                </span>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full px-6 py-16 text-center">
                            <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="mx-auto mb-4 h-12 w-12 text-gray-400" />
                            <p class="text-lg font-semibold text-gray-600">Žiadne nedávne demontáže</p>
                        </div>
                    @endforelse
                </div>

                @if ($this->hasMoreDemontazes)
                    <div
                        id="demontazes-load-more"
                        class="mt-6 flex justify-center py-4"
                        x-data
                        x-init="
                            const observer = new IntersectionObserver((entries) => {
                                entries.forEach((entry) => {
                                    if (entry.isIntersecting) {
                                        $wire.loadMoreDemontazes();
                                    }
                                });
                            }, { rootMargin: '200px' });
                            observer.observe($el);
                        ">
                        <div role="status" class="flex items-center gap-2 text-sm text-gray-500">
                            <svg class="h-5 w-5 animate-spin text-gray-400" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            Načítavam ďalšie...
                        </div>
                    </div>
                @endif
            @endif

            {{-- TAB 2A: Accidents List --}}
            @if ($this->findMode === 'accidents')
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2" id="accidents-grid">
                    @forelse ($this->accidents as $accident)
                        <button
                            type="button"
                            wire:click="selectAccident({{ $accident['id'] }})"
                            title="Otvoriť fotky podzákaziek"
                            class="group rounded-xl border-2 border-gray-200 p-5 text-left transition hover:shadow-md {{ $accident['photo_count'] > 0 ? 'bg-orange-50/70 hover:bg-orange-100' : 'bg-white hover:bg-gray-50' }}">
                            
                            <div class="flex items-center justify-between gap-3">
                                <p class="truncate text-base font-bold text-gray-900">
                                    {{ $accident['vehicle_label'] ?? 'N/A' }}
                                </p>

                                <span class="inline-flex shrink-0 items-center gap-2 rounded-full px-3 py-2 text-sm font-bold {{ $accident['photo_count'] > 0 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600' }}">
                                    <x-filament::icon icon="heroicon-o-photo" class="h-5 w-5" />
                                    {{ $accident['photo_count'] }} {{ $accident['photo_count'] === 1 ? 'fotka' : ($accident['photo_count'] >= 2 && $accident['photo_count'] <= 4 ? 'fotky' : 'fotiek') }}
                                </span>
                            </div>

                            @if ($accident['vehicle_model'])
                                <p class="mt-2 truncate text-sm text-gray-600">
                                    {{ $accident['vehicle_model'] }}
                                </p>
                            @endif

                            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-600">
                                @if ($accident['date'])
                                    <span class="inline-flex items-center gap-2">
                                        <x-filament::icon icon="heroicon-o-calendar" class="h-4 w-4" />
                                        {{ $accident['date'] }}
                                    </span>
                                @endif

                                @if ($accident['item_count'] > 0)
                                    <span class="inline-flex items-center gap-2">
                                        <x-filament::icon icon="heroicon-o-arrow-path" class="h-4 w-4" />
                                        {{ $accident['item_count'] }} podzákazky
                                    </span>
                                @endif

                                @if ($accident['group_title'])
                                    <span class="inline-flex items-center gap-2">
                                        <x-filament::icon icon="heroicon-o-tag" class="h-4 w-4" />
                                        {{ $accident['group_title'] }}
                                    </span>
                                @endif
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full px-6 py-16 text-center">
                            <x-filament::icon icon="fas-bus" class="mx-auto mb-4 h-12 w-12 text-gray-400" />
                            <p class="text-lg font-semibold text-gray-600">Žiadne nedávne havárie</p>
                        </div>
                    @endforelse
                </div>

                @if ($this->hasMoreAccidents)
                    <div
                        id="accidents-load-more"
                        class="mt-6 flex justify-center py-4"
                        x-data
                        x-init="
                            const observer = new IntersectionObserver((entries) => {
                                entries.forEach((entry) => {
                                    if (entry.isIntersecting) {
                                        $wire.loadMoreAccidents();
                                    }
                                });
                            }, { rootMargin: '200px' });
                            observer.observe($el);
                        ">
                        <div role="status" class="flex items-center gap-2 text-sm text-gray-500">
                            <svg class="h-5 w-5 animate-spin text-gray-400" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            Načítavam ďalšie...
                        </div>
                    </div>
                @endif
            @endif

            {{-- TAB 2B: Selected Accident Detail / Sub-tasks --}}
            @if ($this->findMode === 'task' && !empty($taskData['task_id']))
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-3">
                        <span class="inline-flex items-center gap-2 text-sm text-gray-600">
                            <x-filament::icon icon="fas-bus" class="h-5 w-5 text-gray-400" />
                            Vozidlo:
                            <span class="font-semibold text-gray-900">{{ $this->selectedTaskInfo['vehicle_label'] }}</span>
                        </span>

                        <span class="inline-flex items-center gap-2 text-sm text-gray-600">
                            <x-filament::icon icon="heroicon-o-cog-6-tooth" class="h-5 w-5 text-gray-400" />
                            Model:
                            <span class="font-semibold text-gray-900">{{ $this->selectedTaskInfo['vehicle_model'] }}</span>
                        </span>

                        <span class="text-sm text-gray-500">
                            Zákazka #{{ $taskData['task_id'] }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($this->selectedTaskItems as $item)
                        <button
                            type="button"
                            wire:click="openTaskItemPhotos({{ $item['id'] }})"
                            title="Nahrať / spravovať fotky"
                            class="group rounded-xl border-2 border-gray-200 p-5 text-left transition hover:shadow-md {{ $item['photo_count'] > 0 ? 'bg-orange-50/70 hover:bg-orange-100' : 'bg-white hover:bg-gray-50' }}">
                            
                            <div class="flex items-center justify-between gap-3">
                                <p class="truncate text-base font-bold text-gray-900">
                                    {{ $item['group_title'] ?? ('Podzákazka #' . $item['id']) }}
                                </p>

                                <span class="inline-flex shrink-0 items-center gap-2 rounded-full px-3 py-2 text-sm font-bold {{ $item['photo_count'] > 0 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600' }}">
                                    <x-filament::icon icon="heroicon-o-photo" class="h-5 w-5" />
                                    {{ $item['photo_count'] }}
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- TAB 3: Buffer / Fotosérie --}}
            @if ($this->findMode === 'buffer')
                <livewire:dpb.wtftmsbridge.photo-buffer-manager wire:key="photo-buffer-manager" />
            @endif

        </div>
    </section>

</x-filament-panels::page>