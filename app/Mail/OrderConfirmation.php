<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Order; // Import the Order model

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order; // Property to store the order data

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order; // Store the order data
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation', 
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order_confirmation', 
            with: [
                'order' => $this->order,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Build the email message.
     */
    public function build()
    {
        return $this->subject('Order Confirmation') 
                    ->bcc('trakkiezstore@gmail.com') 
                    ->view('emails.order_confirmation') 
                    ->with([
                        'order' => $this->order,
                    ]);
    }
}
