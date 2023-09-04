<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Traits;

use Asdoria\SyliusQuoteRequestPlugin\Storage\QuoteSessionStorageInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;

/**
 *
 */
trait ChannelContextTrait
{
    protected ?ChannelContextInterface $channelContext = null;

    /**
     * @return ChannelContextInterface|null
     */
    public function getChannelContext(): ?ChannelContextInterface
    {
        return $this->channelContext;
    }

    /**
     * @param ChannelContextInterface|null $channelContext
     */
    public function setChannelContext(?ChannelContextInterface $channelContext): void
    {
        $this->channelContext = $channelContext;
    }
}
