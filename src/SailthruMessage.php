<?php

namespace NotificationChannels\Sailthru;

use Illuminate\Support\Arr;

class SailthruMessage
{
    /**
     * The message parameter variables
     *
     * @var array
     */
    protected array $vars = [];

    /**
     * Multi-send EmailVars
     *
     * @var array
     */
    protected array $eVars = [];

    /**
     * The email address the message should be sent from.
     *
     * @var string
     */
    protected string $fromEmail;

    /**
     * The From Name the message should be sent from.
     *
     * @var string
     */
    protected string $fromName;

    /**
     * The email address for the Recipient
     *
     * @var string
     */
    protected string $toEmail;

    /**
     * The Name of the Recipient
     *
     * @var string
     */
    protected string $toName;

    /**
     * The Reply To for the message
     *
     * @var string
     */
    protected string $replyTo;

    /**
     * @var bool
     */
    protected bool $isMultiSend = false;

    /**
     * @var array
     */
    protected array $options = [];

    /**
     * SailthruMessage constructor.
     *
     * @param string $template
     */
    public function __construct(
        protected string $template
    ) {
        $this->fromEmail = config('mail.from.address');
        $this->fromName = config('mail.from.name');
    }

    /**
     * @param string $template
     *
     * @return SailthruMessage
     */
    public static function create(
        string $template
    ): SailthruMessage {
        return new static($template);
    }

    /**
     * @param array $vars
     *
     * @return SailthruMessage
     */
    public function vars(
        array $vars
    ): SailthruMessage {
        $this->vars = $vars;

        return $this;
    }

    /**
     * @param array $eVars
     *
     * @return SailthruMessage
     */
    public function eVars(
        array $eVars
    ): SailthruMessage {
        $this->eVars = $eVars;

        return $this;
    }

    /**
     * @param array $defaultVars
     *
     * @return SailthruMessage
     */
    public function mergeDefaultVars(
        array $defaultVars
    ): SailthruMessage {
        $this->vars = array_merge($defaultVars, $this->getVars());

        return $this;
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function template(
        string $template
    ): SailthruMessage {
        $this->template = $template;

        return $this;
    }

    /**
     * @param array $from Associative array with email and name
     *
     * @return SailthruMessage
     */
    public function from(
        array $from
    ): SailthruMessage {
        $this->fromEmail(
            Arr::get(
                $from,
                'address',
                Arr::get($from, 'email')
            )
        );

        $name = Arr::get($from, 'name');

        if ($name) {
            $this->fromName($name);
        }

        return $this;
    }

    /**
     * @param array $to Associative array with email and name
     *
     * @return SailthruMessage
     */
    public function to(
        array $to
    ): SailthruMessage {
        $this->toEmail(
            Arr::get($to, 'address')
                ?: Arr::get($to, 'email')
        );

        $name = Arr::get($to, 'name');

        if ($name) {
            $this->toName($name);
        }

        return $this;
    }

    /**
     * @param string $toName
     *
     * @return SailthruMessage
     */
    public function toName(
        string $toName
    ): SailthruMessage {
        $this->toName = $toName;

        return $this;
    }

    /**
     * @param string $fromName
     *
     * @return SailthruMessage
     */
    public function fromName(
        string $fromName
    ): SailthruMessage {
        $this->fromName = $fromName;

        return $this;
    }

    /**
     * @param string $toEmail
     *
     * @return SailthruMessage
     */
    public function toEmail(
        string $toEmail
    ): SailthruMessage {
        $this->toEmail = $toEmail;

        return $this;
    }

    /**
     * @param array $toEmails
     *
     * @return SailthruMessage
     */
    public function toEmails(
        array $toEmails
    ): SailthruMessage {
        $this->toEmail = implode(',', $toEmails);
        $this->isMultiSend = true;

        return $this;
    }

    /**
     * @param string $fromEmail
     *
     * @return SailthruMessage
     */
    public function fromEmail(
        string $fromEmail
    ): SailthruMessage {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    /**
     * @param string $replyTo
     *
     * @return SailthruMessage
     */
    public function replyTo(
        string $replyTo
    ): SailthruMessage {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * @param array $options
     *
     * @return SailthruMessage
     */
    public function options(
        array $options
    ): SailthruMessage {
        $this->options = $options;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return array
     */
    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * @return array
     */
    public function getEVars(): array
    {
        return $this->eVars;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        $options = $this->options;

        if ($this->replyTo) {
            $options['replyto'] = $this->replyTo;
        }

        return $options;
    }

    /**
     * @return string
     */
    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    /**
     * @return string
     */
    public function getFromName(): string
    {
        return $this->fromName;
    }

    /**
     * @return string
     */
    public function getToEmail(): string
    {
        return $this->toEmail;
    }

    /**
     * @return string
     */
    public function getToName(): string
    {
        return $this->toName;
    }

    /**
     * @return string
     */
    public function getReplyTo(): string
    {
        return $this->replyTo;
    }

    /**
     * @return bool
     */
    public function isMultiSend(): bool
    {
        return $this->isMultiSend;
    }
}
