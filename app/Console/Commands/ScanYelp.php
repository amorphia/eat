<?php

namespace App\Console\Commands;

use App\Services\ScannerService;
use Illuminate\Console\Command;

class ScanYelp extends Command
{

    protected $scanner;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "scanYelp {zip?} {--zipsCount=5} {--silent : don't send summary email} {--slow : slow the rate of api calls} {--hot : search hot and new}";


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan Yelp for restaurants by zip (or use all columbus zips if none supplied)';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( ScannerService $scannerService )
    {
        $this->scanner = $scannerService;

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

        // if we are doing a hot n new search skip our usual jazz
        if( $this->option( 'hot' ) ){

            $options = [];

            // if we are supplying a zip code manually, add it to our options
            if( $this->argument('zip') ) $options['zip'] = $this->argument('zip');

            // run a hot and new scan
            $this->scanner->scanNewAndHot( $options );
            return;
        }

        // set how many zip codes to scan
        $this->scanner->setZipCount( $this->option( 'zipsCount' ) );

        // run our scan
        $this->scanner->scan( $this->argument('zip'), $this->option( 'slow' ) );
    }
}
