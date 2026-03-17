<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class BackupStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $status;
    public $backupMessageContent;
    public function __construct(string $status, string $backupMessage)
    {
        $this->status = $status;
        $this->backupMessageContent = $backupMessage; 
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Backup Status: {$this->status}")
                    ->view('emails.backup_status')
                    ->with([
                        'status' => $this->status,
                        'backupMessage' => $this->backupMessageContent,
                    ]); 
    }
}
