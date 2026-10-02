<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DataCheckCommand extends Command
{   
    private const PREPROCESSED_DATA_TABLE = 'tmp_kahatova_preprocessed_data';

    protected $signature = 'data:check-worktime {--from=2026-07-01 : Start date} {--department=460 : Department ID}';
    protected $description = 'Check differences between worktime duration and daily operation duration';



    public function handle(): void
    {
        $from = $this->option('from');
        $department = (int) $this->option('department');

        $this->info("Checking worktime from {$from}, department {$department}...");

        $rows = collect();
        // @TODO - needs fixing
        // $rows = DB::table(self::PREPROCESSED_DATA_TABLE . ' as kpd')
        //     ->join('dpb_worktimefund_model_worktime as wt', function ($join) {
        //         $join->on('wt.personal_id', '=', 'kpd.pid')->on('wt.date', '=', 'kpd.date');
        //     })
        //     ->where('wt.department', $department)
        //     ->where('kpd.date', '>=', $from)
        //     ->select([
        //         'kpd.date as o_date',
        //         'kpd.pid',
        //         DB::raw('ROUND(MAX(wt.shift_duration) / 3600, 2) AS wt_dur'),
        //         DB::raw(' ROUND( SUM(kpd.operation_duration / NULLIF(kpd.people_total, 0)) / 3600, 2 ) AS o_dur '),
        //         DB::raw(<<<SQL 
        //             ROUND( 
        //                 (SUM(kpd.operation_duration / NULLIF(kpd.people_total, 0)) / 3600, 2 ) - MAX(wt.shift_duration)) / 3600, 
        //                 2
        //             ) AS diff
        //         SQL),
        //     ])->groupBy('kpd.date', 'kpd.pid')
        //     ->orderBy('diff')
        //     ->get();

        if ($rows->isEmpty()) {
            $this->info('No results found.');
        }

        // print output
        $this->table(
            [
                'Date',
                'PID',
                'Worktime',
                'Operations',
                'Diff',
            ],
            $rows->map(fn($row) => [
                $row->o_date,
                $row->pid,
                $row->wt_dur,
                $row->o_dur,
                $row->diff,
            ])->toArray()
        );

        $this->newLine();

        $this->info("Found {$rows->count()} records.");
    }
}
