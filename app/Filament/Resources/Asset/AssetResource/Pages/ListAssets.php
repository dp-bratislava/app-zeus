<?php

namespace App\Filament\Resources\Asset\AssetResource\Pages;

use App\Filament\Resources\Asset\AssetResource;
use App\Filament\Resources\Asset\AssetResource\Tables\AssetsTable;
use Dpb\WtfTmsBridge\Enums\AssetState;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Asset\AssetResource\Tables\Schemas\MovementApprovalUI;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    public bool $showDismantled = true;
    public bool $showNaSklade = false;
    public bool $showNaVozoch = false;

    public function toggleDismantled(): void
    {
        $this->showDismantled = true;
        $this->showNaSklade = false;
        $this->showNaVozoch = false;
        $this->resetTable();
    }

    public function toggleNaSklade(): void
    {
        $this->showNaSklade = ! $this->showNaSklade;
        $this->showDismantled = false;
        $this->showNaVozoch = false;
        $this->resetTable();
    }

    public function toggleNaVozoch(): void
    {
        $this->showNaVozoch = ! $this->showNaVozoch;
        $this->showDismantled = false;
        $this->showNaSklade = false;
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        $table->pushToolbarActions([
            Action::make('toggleDismantled')
                ->label('Demontované')
                ->color(fn (): string => $this->showDismantled ? 'primary' : 'gray')
                ->action('toggleDismantled'),

            Action::make('toggleNaSklade')
                ->label('Na sklade')
                ->color(fn (): string => $this->showNaSklade ? 'primary' : 'gray')
                ->action('toggleNaSklade'),

            Action::make('toggleNaVozoch')
                ->label('Na vozoch')
                ->color(fn (): string => $this->showNaVozoch ? 'primary' : 'gray')
                ->action('toggleNaVozoch'),
        ]);

        $table->pushToolbarActions([
            MovementApprovalUI::approveBulkAction(),
            MovementApprovalUI::rejectBulkAction(),
        ]);

        return $table;
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        $query = parent::getTableQuery();

        if ($this->showNaSklade) {
            return $query->byState(AssetState::PRIJEM_Z_DIELNE);
        }

        if ($this->showNaVozoch) {
            return $query->byState(AssetState::DIEL_NA_VOZE);
        }

        if ($this->showDismantled) {
            $query->whereNotInState([
                AssetState::VYRADENE_SCHVALENE,
                AssetState::DIEL_NA_VOZE,
                AssetState::PRIJEM_Z_DIELNE,
            ]);
        }

        return $query;
    }
}
