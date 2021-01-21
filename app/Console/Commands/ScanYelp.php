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
    protected $signature = "scanYelp {zip?} {--silent : don't send summary email}";


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

        $this->scanner->scan( $this->argument('zip') );
    }
}
