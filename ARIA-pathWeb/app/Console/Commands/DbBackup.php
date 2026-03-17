<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BackupStatusMail;

class DbBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {who=system}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database backup';

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
        $serverHostname = gethostname(); 
        $serverIP = gethostbyname($serverHostname);
        $backupStatus = 'success';
        $statusMessage = 'The database backup was successful. Server IP: '.$serverIP;
        //Backup the main database
        $filename=$this->argument('who')."_".strtotime(now()).".sql";
        $command="mysqldump --user=".env('DB_USERNAME')." --password=".env('DB_PASSWORD')." --host=".env('DB_HOST')." ".env('DB_DATABASE')." > ".storage_path()."/app/backup/".$filename;
        $output = exec($command, $output_array, $return_value);

        if ($return_value !== 0) {
            Log::error ("Database backup failed\n");
            // Output the error message
            Log::error ("Error in Main DB: $output");
            $backupStatus = 'failure';
            $statusMessage = "Database backup failed. Error in Main DB: $output\n";
        }


        //Backup the 'CentralSlideRegistry' database
        if (env('DB_CENTRALSR_DATABASE')) {
            $filenameCentralSlideDB = $this->argument('who') . "_CentralSlideRegistry_" . strtotime(now()) . ".sql";
            $commandCentralSLideDB = "mysqldump --user=" . env('DB_USERNAME') . " --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_CENTRALSR_DATABASE') . " > " . storage_path() . "/app/backup/" . $filenameCentralSlideDB;
            $csr_output = exec($commandCentralSLideDB, $outputArrayCentral, $returnValueCentral);
        
            if ($returnValueCentral !== 0) {
                Log::error("CentralSlideRegistry backup failed");
                Log::error("Error in Central DB: $csr_output");
                if($backupStatus == 'failure'){
                    $statusMessage .= "CentralSlideRegistry backup failed. Error: $csr_output\n";
                }else{
                    $backupStatus = 'failure';
                    $statusMessage = "CentralSlideRegistry backup failed. Error: $csr_output\n";
                }
            }
        }

        //Backup annotations .db files
        $tarFilename = $this->argument('who')."_annotationDbFiles_".strtotime(now()).".tar.gz";
        $tarFilePath = storage_path()."/app/backup/".$tarFilename;
        $tarSourceDir = public_path("images/annotationDB");

        if (!file_exists($tarSourceDir)) {
            Log::error("Source directory for tar command does not exist: {$tarSourceDir}");
            if($backupStatus == 'failure'){
                $statusMessage .= "Source directory for tar command does not exist: $tarSourceDir\n";
            }else{
                $backupStatus = 'failure';
                $statusMessage = "Source directory for tar command does not exist: $tarSourceDir\n";
            }
        }
        else{
            $tarCommand = "tar -czf {$tarFilePath} -C {$tarSourceDir} .";
            exec($tarCommand, $outputArrayTar, $returnValueTar);

            // Check for errors in archiving
            if ($returnValueTar !== 0) {
                Log::error("Failed to archive the .db files");
                Log::error("Tar Command Output: " . implode("\n", $outputArrayTar));
                if($backupStatus == 'failure'){
                    $statusMessage .= "Failed to archive .db files.";
                }else{
                    $backupStatus = 'failure';
                    $statusMessage = "Failed to archive .db files.";
                }
            }
        }
        // Send the backup status email
        if (env('BACKUP_NOTIFICATION_EMAIL')) {
            Mail::to(env('BACKUP_NOTIFICATION_EMAIL'))->send(new BackupStatusMail((string)$backupStatus, (string)$statusMessage));
        }
        // go to config/app.php to edit backup_limit number
        $this->deleteOldBackups();
        // $sql_files = glob(storage_path("app/backup/*.sql"));
        // usort($sql_files, function($a, $b) {
        //     return filemtime($b) - filemtime($a);
        // });
        // $filesToKeep = array_slice($sql_files, 0, config('app.iv.backup_limit'));

        // foreach ($sql_files as $file) {
        //     if (!in_array($file, $filesToKeep)) {
        //         unlink($file);
        //     }
        // }
    }

    protected function deleteOldBackups()
    {
        $sqlFiles = glob(storage_path("app/backup/*.sql"));
        $tarFiles = glob(storage_path("app/backup/*.tar.gz"));
        $backupFiles = array_merge($sqlFiles, $tarFiles);
    
        usort($backupFiles, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
    
        $filesToKeep = array_slice($backupFiles, 0, config('app.iv.backup_limit'));
    
        foreach ($backupFiles as $file) {
            if (!in_array($file, $filesToKeep)) {
                unlink($file);
            }
        }
    }

}
