<x-filament-panels::page>
    <div>
        <form wire:submit="create">
            {{ $this->form }}
            <div class="pt-4">
                <x-filament::button type="submit">
                    Uložiť
                </x-filament::button>
            </div>
        </form>

        <x-filament-actions::modals />
    </div>

</x-filament-panels::page>
