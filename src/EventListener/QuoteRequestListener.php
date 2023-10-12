<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\EventListener;

use Asdoria\SyliusQuoteRequestPlugin\Event\QuoteRequestEventInterface;
use Asdoria\SyliusQuoteRequestPlugin\Factory\Model\CustomerAfterQuoteRequestFactoryInterface;
use Asdoria\SyliusQuoteRequestPlugin\Mailer\Emails;
use Asdoria\SyliusQuoteRequestPlugin\Traits\QuoteContextTrait;
use Asdoria\SyliusQuoteRequestPlugin\Traits\QuoteSessionStorageTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareTrait;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

/**
 * Class QuoteRequestListener
 *
 * @author Philippe Vesin <pve.asdoria@gmail.com>
 */
class QuoteRequestListener
{
    use QuoteContextTrait;
    use LoggerAwareTrait;

    public function __construct(
        private SenderInterface                           $emailSender,
        protected LocaleContextInterface                  $localeContext
    )
    {
    }

    public function processQuoteRequest(QuoteRequestEventInterface $event): void
    {
        $data       = $event->getData();
        $recipients = $event->getRecipients();

        /** @var OrderInterface $quote */
        $quote = $this->quoteContext->getQuote();

        if ($quote->isEmpty()) return;

        $quoteTokenValue = $data['quoteTokenValue'] ?? null;

        if ($quote->getTokenValue() !== $quoteTokenValue) return;

        try {
            $this->sendContactQuoteRequest($quote, $data, $recipients);
            $this->sendAdminQuoteRequest($quote, $data, $recipients);
            $event->setHasSent(true);
        } catch (\Throwable $exception) {
            $this->logger->critical(sprintf('Not sending Email QUOTE REQUEST into %s::%s', __CLASS__, __METHOD__));
            $this->logger->critical($exception->getMessage());
        }
    }

    /**
     * @param OrderInterface $quote
     * @param array          $data
     * @param array          $recipients
     *
     * @return void
     */
    protected function sendContactQuoteRequest(
        OrderInterface $quote,
        array          $data,
        array          $recipients
    ): void
    {
        $this->emailSender->send(
            Emails::CONTACT_QUOTE_REQUEST,
            [$data['email']],
            [
                'data'       => $data,
                'quote'      => $quote,
                'localeCode' => $this->localeContext->getLocaleCode(),
                'channel'    => $quote->getChannel()
            ]
        );
    }

    /**
     * @param OrderInterface $quote
     * @param array          $data
     *
     * @return void
     */
    protected function sendAdminQuoteRequest(
        OrderInterface $quote,
        array          $data,
        array          $recipients
    ): void
    {
        if (empty($quote->getChannel()->getContactEmail())) return;

        $this->emailSender->send(
            Emails::ADMIN_QUOTE_REQUEST,
            [$quote->getChannel()->getContactEmail()],
            [
                'data'       => $data,
                'quote'      => $quote,
                'localeCode' => $this->localeContext->getLocaleCode(),
                'channel'    => $quote->getChannel()
            ]
        );
    }
}
