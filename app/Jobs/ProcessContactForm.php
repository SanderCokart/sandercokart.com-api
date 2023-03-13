<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ProcessContactForm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly string $name, public readonly string $subject, public readonly string $email, public readonly string $message)
    {

    }

    public function handle(): void
    {
        Mail::raw($this->message, function (Message $message) {
            $message->from($this->email, $this->name);
            $message->to('codehouse@sandercokart.com', 'Sander Cokart');
            $message->subject('CONTACT-FORM: ' . $this->subject);
        });
    }

    public function failed(Throwable $exception): void
    {
        $this->job->delete();
    }

}
