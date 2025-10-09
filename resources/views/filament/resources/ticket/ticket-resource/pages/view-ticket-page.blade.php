<x-filament-panels::page>    
    <div>datum: {{ $ticket->date }}</div>
    <div>nazov: {{ $ticket->title }}</div>
    <div>popis: {{ $ticket->description }}</div>
    <div>stav: {{ $ticket->state->label() }}</div>

    <div>zadal: {{ $ticketHeader?->author?->employee?->last_name }}</div>
    <div>pre stredisko: {{ $ticketHeader?->department?->code }}</div>
    <div>priradene: {{ $ticketHeader?->assignedTo?->employee?->last_name }}</div>

    <div>voz: {{ print_r($ticketSubject?->subject) }}</div>

    <h3 class="font-bold text-2xl">cinnosti / normy</h3>
    @foreach ($activities as $activity)
    <table class="border border-gray-400">
        <thead>
            <tr>
                <th>datum</th>
                <th>cinnost / norma</th>
                <th>predpokl. trvanie</th>
                <th>vykonana praca</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $activity->date->format('Y-m-d') }}</td>
                <td>{{ $activity->template->title }}</td>
                <td>{{ $activity->template->duration . ' min' }}</td>
                <td>
                    @foreach ($workAssignments[$activity->id] as $workAssignment)                        
                        <div>
                            {{ 
                                $workAssignment?->workInterval->date->format('Y-m-d') . ' ' 
                                . $workAssignment?->employeeContract->pid . ' '
                                . $workAssignment?->employeeContract->employee->last_name . ' od: '
                                . $workAssignment?->workInterval->time_from->format('H:i') . ', trvanie: ' 
                                . $workAssignment?->workInterval->duration . ' min'
                            }}
                        </div>
                    @endforeach
                </td>
            </tr>
        </tbody>
    </table>
    @endforeach
</x-filament-panels::page>
