<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command( 'scanYelp' )
            ->dailyAt( '18:15' )
            ->emailOutputOnFailure('jeremy@jeremykalgreen.com' );


        $schedule->command( 'scanYelp --hot' )
            ->weekly()
            ->emailOutputOnFailure('jeremy@jeremykalgreen.com' );

        $schedule->command( 'scanClosed --count=20' )
            ->everyThirtyMinutes()
            ->emailOutputOnFailure('jeremy@jeremykalgreen.com' );
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
