<?php
declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\EmailManager;

use Asdoria\SyliusQuoteRequestPlugin\Event\QuoteRequestEvent;
use Asdoria\SyliusQuoteRequestPlugin\Event\QuoteRequestEventInterface;
use Asdoria\SyliusQuoteRequestPlugin\Traits\QuoteSessionStorageTrait;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManagerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * Class ContactEmailManager
 * @package Asdoria\SyliusQuoteRequestPlugin\EmailManager
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class ContactEmailManager implements ContactEmailManagerInterface
{
    use QuoteSessionStorageTrait;
    public function __construct(
        protected ContactEmailManagerInterface $inner,
        protected EventDispatcherInterface $eventDispatcher,
        protected ChannelContextInterface $channelContext
    )
    {
    }

    /**
     * @param array                 $data
     * @param array                 $recipients
     * @param ChannelInterface|null $channel
     * @param string|null           $localeCode
     *
     * @return void
     */
    public function sendContactRequest(
        array             $data,
        array             $recipients,
        ?ChannelInterface $channel = null,
        ?string           $localeCode = null,
    ): void
    {
        /** @var QuoteRequestEventInterface $event */
        $event = $this->eventDispatcher->dispatch(
            new QuoteRequestEvent($data, $recipients),
            'asdoria_quote_request.contact_email_manager.pre_send'
        );

        if ($event->isHasSent()) {
            $this->getQuoteSessionStorage()->removeForChannel($this->channelContext->getChannel());
            return;
        }

        $this->inner->sendContactRequest($data, $recipients, $channel, $localeCode);
    }
}
