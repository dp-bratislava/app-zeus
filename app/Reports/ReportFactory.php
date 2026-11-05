<?php

namespace App\Reports;

use App\Reports\Drivers\ReportDriver;
use App\Reports\Drivers\DetailReport;
use App\Reports\Drivers\ControllingDetailReport;
use App\Reports\Drivers\BatchReportDriver;
use App\Reports\Drivers\SumarReport;
use App\Reports\Drivers\ControllingReport;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

class ReportFactory
{
    private static array $drivers = [
        'work-activity' => DetailReport::class,
        'sumar' => SumarReport::class,
        'controlling-detail' => ControllingDetailReport::class,
        'controlling' => ControllingReport::class,
    ];

    /**
     * Only reports the current user is allowed to view.
     * A report with a null permission() is public and always included.
     */
    public static function getAvailable(): array
    {
        $available = [];
        foreach (self::$drivers as $key => $driverClass) {
            $driver = new $driverClass();
            $permission = $driver->permission();

            if ($permission === null || Gate::allows($permission)) {
                $available[$key] = $driver;
            }
        }
        return $available;
    }

    public static function canAccess(string $key): bool
    {
        if (!isset(self::$drivers[$key])) {
            return false;
        }

        return (new self::$drivers[$key]())->isAccessible();
    }

    public static function make(string $key): ReportDriver
    {
        if (!isset(self::$drivers[$key])) {
            throw new InvalidArgumentException(
                "Report driver '{$key}' not found. Available: " . implode(', ', array_keys(self::$drivers))
            );
        }

        $driverClass = self::$drivers[$key];
        return new $driverClass();
    }
}

