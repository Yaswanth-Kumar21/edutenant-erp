<?php

namespace App\Mail;

use App\Models\FeePayment;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeePaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly FeePayment $payment,
        public readonly Tenant     $tenant
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Fee Receipt #{$this->payment->receipt_number} — {$this->tenant->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.fee-payment-receipt',
        );
    }
}
