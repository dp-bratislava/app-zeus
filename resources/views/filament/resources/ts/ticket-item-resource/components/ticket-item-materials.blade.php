<div x-show="tab === 'materials'" x-cloak>
    @if ($materials)                
    <table class="min-w-full text-sm text-left text-gray-700 border border-gray-200">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b">datum</th>
                    <th class="px-6 py-3 border-b">cislo materialu</th>
                    <th class="px-6 py-3 border-b">nazov materialu</th>
                    <th class="px-6 py-3 border-b">cena</th>
                    <th class="px-6 py-3 border-b">sadzba DPH</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($materials as $material)
            <tr>
                <td class="px-6 py-4 border-b">{{ $material->date->format('Y-m-d') }}</td>
                <td class="px-6 py-4 border-b">{{ $material->code }}</td>
                <td class="px-6 py-4 border-b">{{ $material->title }}</td>
                <td class="px-6 py-4 border-b">{{ $material->price }}</td>
                <td class="px-6 py-4 border-b">{{ $material->vat . '%'}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>