<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Event;

use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class QuoteRequestEvent
 *
 * @author Philippe Vesin <pve.asdoria@gmail.com>
 */
interface QuoteRequestEventInterface
{
    /**
     * @return array
     */
    public function getData(): array;

    /**
     * @param array $data
     */
    public function setData(array $data): void;

    /**
     * @return array
     */
    public function getRecipients(): array;

    /**
     * @param array $recipients
     */
    public function setRecipients(array $recipients): void;
    
    /**
     * @return bool
     */
    public function isHasSent(): bool;


    /**
     * @param bool $hasSent
     */
    public function setHasSent(bool $hasSent): void;
}
