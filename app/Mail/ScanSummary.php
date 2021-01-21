<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScanSummary extends Mailable
{
    use Queueable, SerializesModels;

    protected $newLocations;
    protected $closedLocations;

    /**
     * Create a new message instance.
     *
     */
    public function __construct( $newLocations, $closedLocations )
    {
        $this->newLocations = $newLocations;
        $this->closedLocations = $closedLocations;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to( config( 'mail.my_email' ) )
            ->subject( "Yelp Scan Summary" )
            ->view('mail.summary')
            ->with([ 'new' => $this->newLocations, 'closed' => $this->closedLocations ]);
    }
}
