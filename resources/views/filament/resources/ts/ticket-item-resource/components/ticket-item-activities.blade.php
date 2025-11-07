<div x-show="tab === 'activities'" x-cloak>
    @if ($activities)
        
    <table class="min-w-full text-sm text-left text-gray-700 border border-gray-200">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b">datum</th>
                    <th class="px-6 py-3 border-b">cinnost / norma</th>
                    <th class="px-6 py-3 border-b">predpokl. trvanie</th>
                    <th class="px-6 py-3 border-b">vykonana praca</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($activities as $activity)
            <tr>
                <td class="px-6 py-4 border-b">{{ $activity->date->format('Y-m-d') }}</td>
                <td class="px-6 py-4 border-b">{{ $activity->template->title }}</td>
                <td class="px-6 py-4 border-b">{{ $activity->template->duration . ' min' }}</td>
                <td class="px-6 py-4 border-b">
                    @if ($workAssignments)
                    <table>
                        <thead>
                            <tr>
                                <th>datum</th>
                                <th>osob. c.</th>
                                <th>meno</th>
                                <th>zaciatok</th>
                                <th>trvanie min</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($workAssignments[$activity->id] as $workAssignment)                        
                            <tr>
                                <td class="px-3 py-2 border-b">{{ $workAssignment?->workInterval?->date?->format('Y-m-d') }}</td>                                                              
                                <td class="px-3 py-2 border-b">{{ $workAssignment?->employeeContract?->pid }}</td>
                                <td class="px-3 py-2 border-b">{{ $workAssignment?->employeeContract?->employee?->last_name }}</td>
                                <td class="px-3 py-2 border-b">{{ $workAssignment?->workInterval?->time_from?->format('H:i') }}</td>
                                <td class="px-3 py-2 border-b">{{ $workAssignment?->workInterval?->duration }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
       
    @endif
</div>