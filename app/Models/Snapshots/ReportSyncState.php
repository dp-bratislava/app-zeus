<?php

namespace App\Models\Snapshots;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ReportSyncState extends Model
{
    protected $table = 'report_sync_state';

    public $timestamps = false;

    protected $guarded = [];

    public function scopeByReportName(Builder $query, string $name): void
    {
        $query->where('report_name', '=', $name);
    }
}
