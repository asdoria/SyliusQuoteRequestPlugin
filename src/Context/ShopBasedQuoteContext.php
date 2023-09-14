<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Context;

use Asdoria\SyliusQuoteRequestPlugin\TokenAssigner\QuoteTokenAssignerInterface;
use Asdoria\SyliusQuoteRequestPlugin\Traits\QuoteSessionStorageTrait;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Symfony\Contracts\Service\ResetInterface;
use Webmozart\Assert\Assert;

/**
 * Class ShopBasedQuoteContext
 * @package Asdoria\SyliusQuoteRequestPlugin\Context
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
final class ShopBasedQuoteContext implements QuoteContextInterface, ResetInterface
{
    use QuoteSessionStorageTrait;
    protected ?OrderInterface $quote = null;

    public function __construct(
        protected QuoteContextInterface $quoteContext,
        protected ShopperContextInterface $shopperContext,
        protected QuoteTokenAssignerInterface $quoteTokenAssigner
    ) {
    }

    public function getQuote(): BaseOrderInterface
    {
        if (null !== $this->quote) {
            return $this->quote;
        }

        try {

            /** @var ChannelInterface $channel */
            $channel = $this->shopperContext->getChannel();
            $this->quote   = $this->getQuoteSessionStorage()->getForChannel($channel);
            if (!empty($this->quote)) {
                return $this->quote;
            }

            $quote = $this->quoteContext->getQuote();

            /** @var OrderInterface $quote */
            Assert::isInstanceOf($quote, OrderInterface::class);
            $quote->setChannel($channel);
            $quote->setCurrencyCode($channel->getBaseCurrency()->getCode());
            $quote->setLocaleCode($this->shopperContext->getLocaleCode());
            $quote->setPaymentState(OrderPaymentStates::STATE_CANCELLED);
            $quote->setShippingState(OrderShippingStates::STATE_CANCELLED);

            $this->quoteTokenAssigner->assignTokenValue($quote);
            $this->getQuoteSessionStorage()->setForChannel($channel, $quote);

        } catch (ChannelNotFoundException | CurrencyNotFoundException | LocaleNotFoundException $exception) {
            throw new QuoteNotFoundException('Sylius was not able to prepare the quote.', $exception);
        }

        $this->quote = $quote;

        return $quote;
    }

    public function reset(): void
    {
        $this->quote = null;
    }

    public function getCart(): BaseOrderInterface
    {
        return $this->getQuote();
    }
}
