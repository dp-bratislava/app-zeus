<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Model;

class BatchReport extends Model
{
    protected $table = 'tmp_asphere_import_batchable_batch_records';

    public $timestamps = false;

    protected $guarded = [];
}
