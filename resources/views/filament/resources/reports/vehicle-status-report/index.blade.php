<x-filament-panels::page>
    <div class="space-y-8">

        <div>
            <h2 class="text-xl font-semibold mb-2">Drivers</h2>
            {{-- {{ $this->vehicleStatusTable }} --}}
        </div>

        <div>
            <h2 class="text-xl font-semibold mb-2">Vehicles</h2>
            {{ $this->table }}
        </div>

    </div>
</x-filament-panels::page>