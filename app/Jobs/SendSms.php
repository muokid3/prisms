<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $msisdn;
    protected $message;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $msisdn, string $message)
    {
        $this->msisdn = $msisdn;
        $this->message = $message;

    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        send_sms("SEARCHTrial", $this->message, $this->msisdn,Carbon::now()->timestamp);
    }
}
