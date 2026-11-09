<x-filament-panels::page>
    <div class="flex flex-col items-center gap-4">
        <x-filament::icon
            icon="heroicon-o-lock-closed"
            class="h-12 w-12 text-danger-500"
        />

        <h2 class="text-xl font-bold">
            Access denied
        </h2>

        <p>{{ $message }}</p>

        <x-filament::button tag="a" :href="$returnUrl">
            Return to Task Batches
        </x-filament::button>
    </div>
</x-filament-panels::page>