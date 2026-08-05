{{-- resources/views/filament/pages/assets-photo-page.blade.php --}}
<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        @if ($data['asset_movement_id'] ?? false)
            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit">
                    Save Photos
                </x-filament::button>
            </div>
        @endif
    </form>

    {{-- <div class="mt-8">
        <h2 class="text-xl font-bold tracking-tight mb-4">Movement & Photo History</h2>

        <div class="space-y-4">
            @forelse ($this->history as $movement)
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ $movement->taskItem?->vehicle?->name ?? 'N/A' }}
                            </span>
                            <span class="text-gray-500 dark:text-gray-400">
                                &bull; {{ $movement->taskItem?->name ?? 'N/A' }}
                                &bull; {{ $movement->name ?? 'Movement #' . $movement->id }}
                            </span>
                        </div>
                        <span class="text-sm text-gray-400">
                            {{ $movement->updated_at->diffForHumans() }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                        @foreach ($movement->getMedia('photos') as $media)
                            <a href="{{ $media->getUrl() }}" target="_blank" class="block aspect-square rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                                <img src="{{ $media->getUrl() }}" alt="Asset photo" class="w-full h-full object-cover hover:scale-105 transition-transform duration-200">
                            </a>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500">
                    No photo history recorded yet.
                </div>
            @endforelse
        </div> --}}
    {{-- </div> --}}
</x-filament-panels::page>