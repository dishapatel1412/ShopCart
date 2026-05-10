<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
// use Illuminate\Mail\Mailables\Attachment;
// use Illuminate\Mail\Mailables\Content;
// use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Order Confirmation - #' . $this->order->id)
            ->view('emails.order_placed')
            ->with([
                'order' => $this->order,
            ]);
    }

    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'New Order Placed',
    //     );
    // }

    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'emails.order_placed', // ✅ FIXED
    //     );
    // }
}
