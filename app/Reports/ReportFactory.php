<?php

namespace App\Reports;

use App\Reports\Drivers\ReportDriver;
use App\Reports\Drivers\WorkActivityReportDriver;
use App\Reports\Drivers\BatchReportDriver;
use App\Reports\Drivers\WorktimeFundPerformanceReportDriver;
use InvalidArgumentException;

class ReportFactory
{
    /**
     * Map of report keys to driver classes
     */
    private static array $drivers = [
        'work-activity' => WorkActivityReportDriver::class,
        'batch' => BatchReportDriver::class,
        'worktime-fund-performance' => WorktimeFundPerformanceReportDriver::class,
        // Add more drivers here as you create them
        // 'attendance' => AttendanceReportDriver::class,
        // 'vehicle-status' => VehicleStatusReportDriver::class,
    ];

    /**
     * Get available report drivers
     */
    public static function getAvailable(): array
    {
        $available = [];
        foreach (self::$drivers as $key => $driverClass) {
            $driver = new $driverClass();
            $available[$key] = $driver;
        }
        return $available;
    }

    /**
     * Make a driver instance by key
     */
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

    /**
     * Get the default driver
     */
    public static function default(): ReportDriver
    {
        return self::make('work-activity');
    }

    /**
     * Register a custom driver
     */
    public static function register(string $key, string $driverClass): void
    {
        self::$drivers[$key] = $driverClass;
    }
}
