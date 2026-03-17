<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;

class CreateThumbnail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:thumbnail {input} {output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate thumbnail when loading image info';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $input=$this->argument('input');
        $output=$this->argument('output');

        if(!file_exists($output)){
            $command="vipsthumbnail -s x200 $input -o $output";
            $res = exec($command, $output_array, $return_value);

            if ($return_value !== 0) {
                Log::error ("Generating thumbnail failed\n");
                // Output the error message
                Log::error ($res);
            }
        }

    }
}
