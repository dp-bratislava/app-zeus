<x-filament-panels::page>
<div>
    <form wire:submit="create">
        {{ $this->form }}
        
        <x-filament::button type="submit">
            Submit
        </x-filament::button>
    </form>
    
    <x-filament-actions::modals />
</div>

</x-filament-panels::page>