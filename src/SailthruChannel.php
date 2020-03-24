<?php

namespace NotificationChannels\Sailthru;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Sailthru_Client;
use Sailthru_Client_Exception;

class SailthruChannel
{
    /**
     * @param Sailthru_Client $sailthru
     */
    public function __construct(
        Sailthru_Client $sailthru
    ) {
        $this->sailthru = $sailthru;
    }

    /**
     * Get default variables that are defined for all emails.
     *
     * Override this to use a different strategy.
     *
     * @return array
     */
    public static function getDefaultVars(): array
    {
        return [];
    }

    /**
     * @param $notifiable
     * @param Notification $notification
     *
     * @return array
     */
    public function send(
        $notifiable,
        Notification $notification
    ) {
        if (config('services.sailthru.enabled') === false) {
            Log::info(
                'Sending Sailthru message',
                [
                    'notifiable' => $notifiable,
                    'notification' => $notification,
                ]
            );

            return [];
        }

        try {
            /** @var SailthruMessage $message */
            $message = $notification->toSailthru($notifiable);
            $message->mergeDefaultVars(
                static::getDefaultVars()
            );

            $response = $message->isMultiSend()
                ? $this->multiSend($message)
                : $this->singleSend($message);

            Event::dispatch(
                new NotificationSent(
                    $notifiable,
                    $notification,
                    static::class,
                    [
                        'message' => $message,
                        'response' => $response,
                    ]
                )
            );

            return $response;
        } catch (Sailthru_Client_Exception $e) {
            Event::dispatch(
                new NotificationFailed(
                    $notifiable,
                    $notification,
                    static::class,
                    [
                        'message' => $message,
                        'exception' => $e,
                    ]
                )
            );

            return [];
        }
    }

    /**
     * @param SailthruMessage $sailthruMessage
     *
     * @throws Sailthru_Client_Exception
     *
     * @return array
     */
    protected function multiSend(
        SailthruMessage $sailthruMessage
    ) {
        $template = $sailthruMessage->getTemplate();
        $toEmail = $sailthruMessage->getToEmail();
        $vars = $sailthruMessage->getVars();
        $eVars = $sailthruMessage->getEVars();
        $options = $sailthruMessage->getOptions();

        if (config('services.sailthru.log_payload') === true) {
            Log::debug(
                'Sailthru Payload',
                [
                    'template' => $template,
                    'email' => $toEmail,
                    'vars' => $vars,
                    'eVars' => $eVars,
                    'options' => $options,
                ]
            );
        }

        return $this->sailthru->multisend(
            $template,
            $toEmail,
            $vars,
            $eVars,
            $options
        );
    }

    /**
     * @param SailthruMessage $sailthruMessage
     *
     * @throws Sailthru_Client_Exception
     *
     * @return array
     */
    protected function singleSend(
        SailthruMessage $sailthruMessage
    ) {
        $template = $sailthruMessage->getTemplate();
        $toEmail = $sailthruMessage->getToEmail();
        $vars = $sailthruMessage->getVars();
        $options = $sailthruMessage->getOptions();

        if (config('services.sailthru.log_payload') === true) {
            Log::debug(
                'Sailthru Payload',
                [
                    'template' => $template,
                    'email' => $toEmail,
                    'vars' => $vars,
                    'options' => $options,
                ]
            );
        }

        return $this->sailthru->send(
            $template,
            $toEmail,
            $vars,
            $options
        );
    }
}
