<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewContactMessage extends Mailable
{
    public function __construct(public ContactMessage $contact) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New message from the website: '.__('site.contact.topics.'.$this->contact->topic, [], 'en'),
            replyTo: [new Address($this->contact->email, $this->contact->name)],
        );
    }

    public function content(): Content
    {
        return new Content(text: 'mail.contact-message');
    }
}
