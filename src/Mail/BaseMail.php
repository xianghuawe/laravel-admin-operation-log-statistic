<?php

namespace Xianghuawe\Admin\Mail;

use Illuminate\Mail\Mailable;

abstract class BaseMail extends Mailable
{
    abstract public function envelope(): \Illuminate\Mail\Mailables\Envelope;
}
