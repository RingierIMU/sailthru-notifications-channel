<?php

namespace NotificationChannels\Sailthru;

use Illuminate\Support\Arr;

class SailthruMessage
{
    /**
     * The message parameter variables
     */
    protected array $vars = [];

    /**
     * Multi-send EmailVars
     */
    protected array $eVars = [];

    /**
     * The email address the message should be sent from.
     */
    protected string $fromEmail;

    /**
     * The From Name the message should be sent from.
     */
    protected string $fromName;

    /**
     * The email address for the Recipient
     */
    protected string $toEmail;

    /**
     * The Name of the Recipient
     */
    protected string $toName;

    /**
     * The Reply To for the message
     */
    protected string $replyTo;

    protected bool $isMultiSend = false;

    protected array $options = [];

    /**
     * SailthruMessage constructor.
     */
    public function __construct(
        protected string $template,
    ) {
        $this->fromEmail = config('mail.from.address');
        $this->fromName = config('mail.from.name');
    }

    public static function create(
        string $template,
    ): SailthruMessage {
        return new static($template);
    }

    public function vars(
        array $vars,
    ): SailthruMessage {
        $this->vars = $vars;

        return $this;
    }

    public function eVars(
        array $eVars,
    ): SailthruMessage {
        $this->eVars = $eVars;

        return $this;
    }

    public function mergeDefaultVars(
        array $defaultVars,
    ): SailthruMessage {
        $this->vars = array_merge($defaultVars, $this->getVars());

        return $this;
    }

    /**
     * @return $this
     */
    public function template(
        string $template,
    ): SailthruMessage {
        $this->template = $template;

        return $this;
    }

    /**
     * @param array $from Associative array with email and name
     */
    public function from(
        array $from,
    ): SailthruMessage {
        $this->fromEmail(
            Arr::get(
                $from,
                'address',
                Arr::get($from, 'email'),
            ),
        );

        $name = Arr::get($from, 'name');

        if ($name) {
            $this->fromName($name);
        }

        return $this;
    }

    /**
     * @param array $to Associative array with email and name
     */
    public function to(
        array $to,
    ): SailthruMessage {
        $this->toEmail(
            Arr::get($to, 'address')
                ?: Arr::get($to, 'email'),
        );

        $name = Arr::get($to, 'name');

        if ($name) {
            $this->toName($name);
        }

        return $this;
    }

    public function toName(
        string $toName,
    ): SailthruMessage {
        $this->toName = $toName;

        return $this;
    }

    public function fromName(
        string $fromName,
    ): SailthruMessage {
        $this->fromName = $fromName;

        return $this;
    }

    public function toEmail(
        string $toEmail,
    ): SailthruMessage {
        $this->toEmail = $toEmail;

        return $this;
    }

    public function toEmails(
        array $toEmails,
    ): SailthruMessage {
        $this->toEmail = implode(',', $toEmails);
        $this->isMultiSend = true;

        return $this;
    }

    public function fromEmail(
        string $fromEmail,
    ): SailthruMessage {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    public function replyTo(
        string $replyTo,
    ): SailthruMessage {
        $this->replyTo = $replyTo;

        return $this;
    }

    public function options(
        array $options,
    ): SailthruMessage {
        $this->options = $options;

        return $this;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getVars(): array
    {
        return $this->vars;
    }

    public function getEVars(): array
    {
        return $this->eVars;
    }

    public function getOptions(): array
    {
        $options = $this->options;

        if ($this->replyTo) {
            $options['replyto'] = $this->replyTo;
        }

        return $options;
    }

    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    public function getFromName(): string
    {
        return $this->fromName;
    }

    public function getToEmail(): string
    {
        return $this->toEmail;
    }

    public function getToName(): string
    {
        return $this->toName;
    }

    public function getReplyTo(): string
    {
        return $this->replyTo;
    }

    public function isMultiSend(): bool
    {
        return $this->isMultiSend;
    }
}
