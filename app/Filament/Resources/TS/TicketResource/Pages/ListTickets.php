<?php

namespace App\Filament\Resources\TS\TicketResource\Pages;

use App\Filament\Resources\TS\TicketResource;
use App\Services\TS\CreateTicketService;
use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Tickets\Models\TicketGroup;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()
            //     ->modalWidth(MaxWidth::MaxContent) // options: sm, md, lg, xl, 2xl
            //     // ->using(function (array $data, string $model, SubjectService $ticketSubjectSvc, HeaderService $ticketHeaderService): ?Model {
            //     // ->using(function (array $data, string $model, CreateTicketService $ticketSvc): ?Model {
            //     //     dd('hh');
            //     //     return $ticketSvc->create($data);
            //     // })

            //     ->using(function (array $data, TicketAssignmentRepository $ticketAssignmentRepository): ?Model {
            //         // dd('hh');
            //         return $ticketAssignmentRepository->create($data);
            //     })
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return '';
    }    

    public function getTabs(): array
    {
        $tabs = [];

        // Default “all” tab
        $tabs['all'] = Tab::make('Všetky');

        // Dynamic tabs
        foreach (TicketGroup::get() as $group) {
            $tabs[$group->code] = Tab::make($group->title)
                ->modifyQueryUsing(
                    function(Builder $query) use ($group) {
                        $query->whereHas('ticket', function($q) use ($group) {
                            $q->byGroup($group->code);
                        });
                        // return Ticket::query()->merge($query)->byGroup($group->code);
                    }
                );
        }

        return $tabs;
    }
}
