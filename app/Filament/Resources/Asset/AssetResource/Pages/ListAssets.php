<?php

namespace App\Filament\Resources\Asset\AssetResource\Pages;

use App\Filament\Resources\Asset\AssetResource;
use App\Filament\Resources\Asset\AssetResource\Tables\AssetsTable;
use Dpb\Package\Assets\Models\Asset;
use Dpb\Package\Assets\Models\AssetMovement;
use Dpb\WtfTmsBridge\Enums\AssetState;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;
use Dpb\Package\Assets\Enums\ApprovalStatus;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    public bool $showNaSklade = false;
    public bool $showNaVozoch = false;
    public bool $showRequiresApproval = false;

    public function toggleNaSklade(): void
    {
        $this->showNaSklade = ! $this->showNaSklade;
        $this->showNaVozoch = false;
        $this->showRequiresApproval = false;
        $this->resetTable();
    }

    public function toggleNaVozoch(): void
    {
        $this->showNaVozoch = ! $this->showNaVozoch;
        $this->showNaSklade = false;
        $this->showRequiresApproval = false;
        $this->resetTable();
    }

    public function toggleRequiresApproval(): void
    {
        $this->showRequiresApproval = ! $this->showRequiresApproval;
        $this->showNaSklade = false;
        $this->showNaVozoch = false;
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        $table->pushToolbarActions([
            Action::make('toggleNaSklade')
                ->label('Na sklade')
                ->color(fn (): string => $this->showNaSklade ? 'primary' : 'gray')
                ->action('toggleNaSklade'),

            Action::make('toggleNaVozoch')
                ->label('Na vozoch')
                ->color(fn (): string => $this->showNaVozoch ? 'primary' : 'gray')
                ->action('toggleNaVozoch'),

            Action::make('toggleRequiresApproval')
                ->label('Vyžadujú schválenie')
                ->color(fn (): string => $this->showRequiresApproval ? 'primary' : 'gray')
                ->action('toggleRequiresApproval'),
        ]);

        if ($this->showRequiresApproval) {
            $table->pushBulkActions([
                AssetsTable::approveBulkAction(),
                AssetsTable::rejectBulkAction(),
            ]);
        }

        return $table;
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        $query = parent::getTableQuery();

        if ($this->showRequiresApproval) {
            return $query->whereHas('latestMovement', function (Builder $movementQuery) {
                $movementQuery->where('approval_status', '=', ApprovalStatus::PENDING);
            });
        }

        if ($this->showNaSklade) {
            return $query->byState(AssetState::PRIJEM_Z_DIELNE);
        }

        if ($this->showNaVozoch) {
            return $query->byState(AssetState::DIEL_NA_VOZE);
        }
        $query->whereNotInState([
                AssetState::VYRADENE_SCHVALENE,
                AssetState::DIEL_NA_VOZE,
                AssetState::PRIJEM_Z_DIELNE,
            ]);
        return $query;
    }
}


