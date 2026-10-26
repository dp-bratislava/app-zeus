<?php

namespace App\Models\Reports;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WorkActivityReport extends Model
{
    protected $table = 'mvw_work_activity_report_v3';

    public $timestamps = false;

    protected $guarded = [];

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => ucfirst($this->last_name).' '.ucfirst($this->first_name),
        );
    }

    public static function getLastSyncedAt(): ?string
    {
        $lastSyncedAt = DB::table('report_sync_state')
            ->where('report_name', 'work-activity')
            ->value('last_synced_at');

        return $lastSyncedAt ? Carbon::parse($lastSyncedAt)->format('d.m.Y H:i') : null;
    }
}

