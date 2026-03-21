<?php

namespace App\Mail;

use App\Models\Agendamento;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgendamentoConfirmadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Agendamento $agendamento,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmacao de agendamento - JC Barber',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.agendamento-confirmado',
        );
    }
}

