<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DbRestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore {sql}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore database by backup sql';

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
        $filename=$this->argument('sql');
        $command="mysql --user=".env('DB_USERNAME')." --password=".env('DB_PASSWORD')." --host=".env('DB_HOST')." ".env('DB_DATABASE')." < ".storage_path()."/app/backup/".$filename;
        $output = exec($command, $output_array, $return_value);

        if ($return_value !== 0) {
            Log::error ("Database restore failed\n");
            // Output the error message
            Log::error ("Error: $output");
        }
    }
}
