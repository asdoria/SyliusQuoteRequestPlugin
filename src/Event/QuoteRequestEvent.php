<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class QuoteRequestEvent
 * @package Asdoria\SyliusQuoteRequestPlugin\Event
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class QuoteRequestEvent extends Event implements QuoteRequestEventInterface
{
    protected bool $hasSent = false;

    /**
     * @param array $data
     * @param array $recipients
     */
    public function __construct(
        protected array $data,
        protected array $recipients
    )
    {
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @param array $recipients
     */
    public function setRecipients(array $recipients): void
    {
        $this->recipients = $recipients;
    }

    /**
     * @return bool
     */
    public function isHasSent(): bool
    {
        return $this->hasSent;
    }

    /**
     * @param bool $hasSent
     */
    public function setHasSent(bool $hasSent): void
    {
        $this->hasSent = $hasSent;
    }
}
