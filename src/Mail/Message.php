<?php


namespace App\Mail;


class Message
{
    private string $from;
    private string $to;
    private string $content;
    private string $subject;

    public function __construct(string $from, string $to, string $content, string $subject) {
        $this->from = $from;
        $this->to = $to;
        $this->content = $content;
        $this->subject = $subject;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
