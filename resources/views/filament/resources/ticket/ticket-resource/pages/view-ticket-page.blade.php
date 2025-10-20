<x-filament-panels::page>  

<x-filament::button
    icon="heroicon-m-chevron-double-left"
    color="primary"
    tag="a"
    href="{{ route('filament.admin.resources.ticket.tickets.index') }}"
>
    Zákazky
</x-filament::button>

@if ($ticket->parent)
    <div>patri pod: <a href="{{ route('filament.admin.resources.ticket.tickets.view', ['record' => $ticket->parent->id]) }}">{{ $ticket->parent->id . ' - '. $ticket->parent->title }}</div>    
@endif

<div class="grid grid-cols-3 gap-4">
  <div class="bg-gray-300 p-4">    
    <div>datum: {{ $ticket->date }}</div>
    <div>nazov: {{ $ticket->title }}</div>
    <div>popis: {{ $ticket->description }}</div>
    <div>stav: {{ $ticket?->state?->label() }}</div>
</div>
  <div class="bg-gray-300 p-4">
    <div>zadal: {{ $ticketHeader?->author?->employee?->last_name }}</div>
    <div>pre stredisko: {{ $ticketHeader?->department?->code }}</div>
    <div>priradene: {{ $ticketHeader?->assignedTo?->employee?->last_name }}</div>

    {{-- <div>voz: {{ print_r($ticketSubjectsubject) }}</div> --}}
    <div>voz: {{ $ticketSubject?->code?->code }}</div>
  </div>
  <div class="bg-gray-300 p-4">
    <div>trvanie predpokladane/realne: {{ $totalExpectedDuration . ' min /' . $totalDuration . ' min'}}</div>
    <div>naklady material: {{ $totalMaterialExpenses . ' EUR' }}</div>
    <div>naklady sluzby: {{ $totalServiceExpenses . ' EUR'}}</div>
  </div>
</div>    

<div x-data="{ tab: 'activities' }" class="w-full">
    <div class="flex border-b border-gray-200 space-x-6 mb-4">
        <button 
            :class="tab === 'activities' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-6 pb-2 border-b-2 text-sm font-medium"
            @click="tab = 'activities'">
            Cinnosti / normy
        </button>

        <button 
            :class="tab === 'materials' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-6 pb-2 border-b-2 text-sm font-medium"
            @click="tab = 'materials'">
            Materialy
        </button>

        <button 
            :class="tab === 'services' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-6 pb-2 border-b-2 text-sm font-medium"
            @click="tab = 'services'">
            Sluzby
        </button>
    </div>

    <div x-show="tab === 'activities'" x-cloak>
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
                                <td class="px-3 py-2 border-b">{{ $workAssignment?->workInterval->date->format('Y-m-d') }}</td>                                                              
                                <td class="px-3 py-2 border-b">{{ $workAssignment?->employeeContract->pid }}</td>
                                <td class="px-3 py-2 border-b">{{ $workAssignment?->employeeContract->employee->last_name }}</td>
                                <td class="px-3 py-2 border-b">{{ $workAssignment?->workInterval->time_from->format('H:i') }}</td>
                                <td class="px-3 py-2 border-b">{{ $workAssignment?->workInterval->duration }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    <div x-show="tab === 'materials'" x-cloak>
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
    </div>

    <div x-show="tab === 'services'" x-cloak>
    <table class="min-w-full text-sm text-left text-gray-700 border border-gray-200">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b">datum</th>
                    <th class="px-6 py-3 border-b">kod sluzby</th>
                    <th class="px-6 py-3 border-b">nazov sluzby</th>
                    <th class="px-6 py-3 border-b">popis</th>
                    <th class="px-6 py-3 border-b">cena</th>
                    <th class="px-6 py-3 border-b">sadzba DPH</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($services as $service)
            <tr>
                <td class="px-6 py-4 border-b">{{ $service->date->format('Y-m-d') }}</td>
                <td class="px-6 py-4 border-b">{{ $service->code }}</td>
                <td class="px-6 py-4 border-b">{{ $service->title }}</td>
                <td class="px-6 py-4 border-b">{{ $service->description }}</td>
                <td class="px-6 py-4 border-b">{{ $service->price }}</td>
                <td class="px-6 py-4 border-b">{{ $service->vat . '%'}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>

</x-filament-panels::page>
