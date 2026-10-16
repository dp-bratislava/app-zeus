<?php

namespace App\Reports;

use App\Reports\Drivers\ReportDriver;
use App\Reports\Drivers\DetailReport;
use App\Reports\Drivers\KontrolingDetailReport;
use App\Reports\Drivers\BatchReportDriver;
use App\Reports\Drivers\SumarReport;
use App\Reports\Drivers\KontrolingReport;
use InvalidArgumentException;

class ReportFactory
{
    private static array $drivers = [
        'work-activity' => DetailReport::class,
        'sumar' => SumarReport::class,
        'kontroling-detail' => KontrolingDetailReport::class,
        'kontroling' => KontrolingReport::class,
        // Add more drivers here as you create them
    ];

    public static function getAvailable(): array
    {
        $available = [];
        foreach (self::$drivers as $key => $driverClass) {
            $driver = new $driverClass();
            $available[$key] = $driver;
        }
        return $available;
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

