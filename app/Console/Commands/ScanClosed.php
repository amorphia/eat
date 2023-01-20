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
    protected $signature = "scanClosed {--count=100} {--silent=true : don't send summary email} {--slow=true : slow the rate of api calls}";


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
        // sell scanner to send summary email
        if( $this->option( 'silent' ) ) $this->scanner->setSummary( false );

        // set how many zip codes to scan
        $this->scanner->setClosedCount( $this->option( 'count' ) );

        // run our scan
        $this->scanner->scan( $this->option( 'slow' ) );
    }
}