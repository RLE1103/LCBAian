<?php

namespace App\Mail;

use App\Models\JobPost;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobApprovedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $job;
    public $approved;

    /**
     * Create a new message instance.
     */
    public function __construct(JobPost $job, bool $approved = true)
    {
        $this->job = $job;
        $this->approved = $approved;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->approved 
            ? 'Your Job Posting Has Been Approved!' 
            : 'Job Posting Status Update';
            
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.job-approved',
            with: [
                'jobTitle' => $this->job->title,
                'approved' => $this->approved,
                'jobUrl' => env('FRONTEND_URL', 'http://localhost:5173') . '/jobs?id=' . $this->job->id,
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
}
