<?php

namespace App\Filament\Resources\Inspection\InspectionTemplateResource\Forms;

use App\Filament\Resources\Fleet\Vehicle\BrandResource\Forms\BrandPicker;
use App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Forms\VehicleTypePicker;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Dpb\Package\Activities\Models\TemplateGroup as ActivityTemplateGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Fleet\Models\VehicleModel;
use Dpb\Package\Tickets\Models\TicketItemGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;

class TicketItemGroupsTab
{
    public static function make(): array
    {
        return [
            Forms\Components\Section::make()
                // @todo
                ->description('zoznam typov podzákazok / normočinností alebo niečoho, čo sa viaže k danej kontrole, takže keď sa vytorí zákazka z kontroly, tak by sa automaticky mohli založiť príslušné podzákazky')
                ->schema([
                    Forms\Components\ToggleButtons::make('main_ticket_item_groups')
                        ->label(__('inspections/inspection-template.form.fields.ticket_item_groups'))
                        ->options(function () {
                            return ActivityTemplateGroup::whereNull('parent_id')
                                ->get()
                                ->mapWithKeys(fn($ticketItemGroup) => [
                                    $ticketItemGroup->id => $ticketItemGroup->title
                                ]);
                        })
                        // ->inline()
                        ->live()
                        ->columns(4),
                    //
                    Forms\Components\CheckboxList::make('ticket_item_groups')
                        ->label(__('inspections/inspection-template.form.fields.ticket_item_groups'))
                        ->options(function (Get $get) {
                            // return TicketItemGroup::get()
                            $templateGroupId = $get('main_ticket_item_groups');
                            if ($templateGroupId === null) {
                                return [];
                            }

                            return ActivityTemplate::byTemplateGroupId($templateGroupId)
                                ->get()
                                ->mapWithKeys(fn($ticketItemGroup) => [
                                    $ticketItemGroup->id => $ticketItemGroup->title
                                ]);
                        })
                        ->searchable()
                        ->bulkToggleable(true)
                        ->columnSpanFull()
                        ->columns(6)
                ]),
        ];
    }
}
