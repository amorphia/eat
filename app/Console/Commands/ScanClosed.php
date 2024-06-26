<?php

namespace App\Console\Commands;

use App\Services\ClosedService;
use Illuminate\Console\Command;

class ScanClosed extends Command
{

    protected $scanner;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "scanClosed {--count=100}";


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan Yelp for closed restaurants';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( ClosedService $closedService )
    {
        $this->scanner = $closedService;

        // add console output to scanner
        $this->scanner->setConsole( $this );

        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // set how many locations to scan
        $this->scanner->setClosedCount( $this->option( 'count' ) );

        // run our scan
        $this->scanner->scan();
    }
}
