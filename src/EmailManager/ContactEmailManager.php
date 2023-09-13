<?php
declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\EmailManager;

use Asdoria\SyliusQuoteRequestPlugin\Event\QuoteRequestEvent;
use Asdoria\SyliusQuoteRequestPlugin\Event\QuoteRequestEventInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * Class ContactEmailManager
 * @package Asdoria\SyliusQuoteRequestPlugin\EmailManager
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class ContactEmailManager implements ContactEmailManagerInterface
{
    public function __construct(
        protected ContactEmailManagerInterface $inner,
        protected EventDispatcherInterface $eventDispatcher
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

        if (!$event->isHasSent()){
            $this->inner->sendContactRequest($data, $recipients, $channel, $localeCode);
        }
    }
}
