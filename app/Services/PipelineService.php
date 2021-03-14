<?php


namespace App\Services;

use Illuminate\Pipeline\Pipeline;

class PipelineService
{

    /**
     * Our pipeline object
     *
     * @var \Illuminate\Pipeline\Pipeline;
     */
    protected $pipeline;


    public function __construct()
    {
        $this->pipeline = app( Pipeline::class );
    }

    /**
     * Resolve our pipeline by sending our subject through our array of
     * pipes then return our subject
     *
     * @param mixed $subject
     * @param array $pipes
     * @return mixed
     */
    public function resolve( $subject, array $pipes ){
        return $this->pipeline
                    ->send( $subject )
                    ->through( $pipes )
                    ->thenReturn();
    }
}
