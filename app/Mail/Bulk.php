<?php

namespace App\Mail;

use App\BulkMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Bulk extends Mailable
{
    use Queueable, SerializesModels;

    public $bulkMail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(BulkMail $bulkMail)
    {
        $this->bulkMail = $bulkMail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->bulkMail->subject)
            ->markdown('emails.bulk');
    }
}
