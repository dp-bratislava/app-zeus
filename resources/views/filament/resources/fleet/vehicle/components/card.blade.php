<div @class([
    'shadow rounded-lg p-4 hover:shadow-lg transition duration-200',
    'bg-green-200' => $vehicle['state'] === 'in-service',
    'bg-yellow-200' => $vehicle['state'] === 'under-repair',
    'bg-red-200' => $vehicle['state'] === 'missing-parts',
])>
    <div class="flex">
        <div class="w-1/4">
            <h2 class="text-lg font-bold">{{ $vehicle['code_1'] }}</h2>
        </div>
        <div class="w-3/4">
            <p class="text-gray-600">Model: {{ $vehicle['model']['title'] ?? 'N/A' }}</p>
            {{-- <p class="text-gray-600">Brand: {{ 'N/A' }}</p> --}}
            {{-- <p class="text-gray-500 text-sm">ID: {{ $vehicle['id'] }}</p> --}}
        </div>
    </div>

    <p class="text-gray-500 text-sm">State: {{ $vehicle['state'] }}</p>

    {{-- <div class="mt-2 flex justify-between">
        <a href="{{ route('filament.resources.vehicles.edit', $vehicle) }}"
            class="text-sm text-blue-600 hover:underline">Edit</a>
        <span class="text-sm text-gray-400">{{ $vehicle['created_at']->format('Y-m-d') }}</span>
    </div> --}}
</div>
