<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CreateDroplet::class,
        Commands\DeleteDroplet::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $jsonString = file_get_contents(base_path('schedules.json'));

        $schedule_data = json_decode($jsonString, true);

        if (!count($schedule_data)) {
            return;
        }

        foreach ($schedule_data as $key => $single_schedule) {
            $schedule
                ->command($single_schedule['command'])
                ->cron($single_schedule['cron']);
        }
    }
}
