<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\EventListener;

use Asdoria\SyliusQuoteRequestPlugin\Event\QuoteRequestEventInterface;
use Asdoria\SyliusQuoteRequestPlugin\Factory\Model\CustomerAfterQuoteRequestFactoryInterface;
use Asdoria\SyliusQuoteRequestPlugin\Mailer\Emails;
use Asdoria\SyliusQuoteRequestPlugin\Traits\QuoteContextTrait;
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
        private CustomerAfterQuoteRequestFactoryInterface $customerAfterQuoteRequestFactory,
        protected EntityManagerInterface                  $entityManager,
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

        if ($quote->getTokenValue() !== $data['quoteTokenValue']) return;

        try {
            $quote = $this->entityManager->wrapInTransaction(function (EntityManagerInterface $manager) use ($quote, $data) {
                $quote->setCustomer($this->customerAfterQuoteRequestFactory->createAfterQuoteRequest($quote, $data));
                $quote->setState(OrderInterface::STATE_CANCELLED);
                return $quote;
            });

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

        if (empty($quote->getChannel()->getContactEmail())) return;

        $this->emailSender->send(
            Emails::CONTACT_QUOTE_REQUEST,
            $recipients,
            [
                'data'       => $data,
                'quote'      => $quote,
                'localeCode' => $this->localeContext->getLocaleCode(),
                'channel'    => $quote->getChannel()
            ],
            [],
            [$data['email']],
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
        array          $data
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
