<x-filament-panels::page>
    {{-- `$this->getRecord()` will return the current Eloquent record for this page --}}
    
    {{ $this->content }} {{-- This will render the content of the page defined in the `content()` method, which can be removed if you want to start from scratch --}}
</x-filament-panels::page>