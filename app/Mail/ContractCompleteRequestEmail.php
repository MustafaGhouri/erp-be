<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractCompleteRequestEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $contract;
    /**
     * Create a new message instance.
     */
    public function __construct($contract)
    {
        $this->contract = $contract;
    }

    public function build()
    {
        return $this->subject('Contract Complete Request')
            ->view('emails.contract_complete_request')
            ->with([
                'contract' => $this->contract,
            ]);
    }
}
