<?php

namespace App\Filament\Resources\TS\TicketResource\RelationManagers;

use App\Filament\Resources\TS\TicketItemResource\Forms\TicketItemForm;
use App\Filament\Resources\TS\TicketItemResource\Tables\TicketItemTable;
use App\Services\TicketItemRepository;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketItemRelationManager extends RelationManager
{
    protected static string $relationship = 'ticketItems';

    public function form(Form $form): Form
    {
        return TicketItemForm::make($form);
    }

    public function table(Table $table): Table
    {
        return TicketItemTable::make($table)
            ->headerActions([
                CreateAction::make()
                    // ->mutateFormDataUsing(function (array $data) {
                    //     $data['assigned_to'] = 1;
                    //     return $data;
                    // })
                    ->using(function (array $data, TicketItemRepository $ticketItemRepo): ?Model {
                        // dd($data);
                        $data['ticket_id'] = $this->getOwnerRecord()->id;
                        return $ticketItemRepo->create($data);
                    })
                    ->modalWidth(MaxWidth::class),
            ]);
    }
}
