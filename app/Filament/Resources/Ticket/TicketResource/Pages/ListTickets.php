<?php

namespace App\Filament\Resources\Ticket\TicketResource\Pages;

use App\Filament\Resources\Ticket\TicketResource;
use App\Models\TicketSubject;
use App\Services\Ticket\ActivityService;
use App\Services\Ticket\CreateTicketService;
use App\Services\Ticket\HeaderService;
use App\Services\Ticket\SubjectService;
use App\Services\TicketService;
use App\States\Ticket\Created;
use Dpb\DatahubSync\Models\Department;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Model;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth(MaxWidth::MaxContent) // options: sm, md, lg, xl, 2xl
                // ->using(function (array $data, string $model, SubjectService $ticketSubjectSvc, HeaderService $ticketHeaderService): ?Model {
                ->using(function (array $data, string $model, CreateTicketService $ticketSvc): ?Model {
                    return $ticketSvc->create($data);
                    // dd($data);
                    // // services
                    // $materials = $data['materials'];
                    // // materials
                    // $services = $data['services'];

                    // $ticket = $model::create([
                    //     'date' => $data['date'],
                    //     'title' => $data['title'],
                    //     'description' => $data['description'],
                    //     'source_id' => $data['source_id'],
                    //     'state' => Created::$name
                    // ]);

                    // $ticketSubjectSvc->setSubject($ticket, Vehicle::find($data['subject_id']));
                    // return $ticket;
                })            
            // ->after(function (TicketService $ticketService, Department $departmentHdl) {
            //     $data = $this->form->getState();
            //     $department = $departmentHdl->findOrFail($data['department_id']);

            //     $ticketService->assignDepartment($this->record, $department);
            // }),
        ];
    }
}
