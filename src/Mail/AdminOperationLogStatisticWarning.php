<?php

namespace Xianghuawe\Admin\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AdminOperationLogStatisticWarning extends BaseMail
{
    use Queueable, SerializesModels;

    protected Collection $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public Carbon $date)
    {
        $this->data = config('admin.database.operation_statistic_model')::whereDate('date', $date->toDateString())->orderByDesc('total')->limit(10)->get();
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('support@cardbrother.com', 'Card Brother'),
            subject: sprintf('[%s]操作记录预警-[%s]', $this->date->toDateString(), config('app.name')),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'operation-statistic::mail.admin-operation-log-statistic-warning',
            with: [
                'riskRecords' => $this->data,
                'riskDate'    => $this->date->toDateString(),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
