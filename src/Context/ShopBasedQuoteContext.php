<?php


declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Context;

use Asdoria\SyliusQuoteRequestPlugin\Context\Model\QuoteContextInterface;
use Asdoria\SyliusQuoteRequestPlugin\TokenAssigner\QuoteTokenAssignerInterface;
use Asdoria\SyliusQuoteRequestPlugin\Traits\QuoteSessionStorageTrait;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Order\Context\CartContextInterface;
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
            $this->quoteTokenAssigner->assignTokenValue($quote);
            $this->getQuoteSessionStorage()->setForChannel($channel, $quote);

        } catch (ChannelNotFoundException | CurrencyNotFoundException | LocaleNotFoundException $exception) {
            throw new QuoteNotFoundException('Sylius was not able to prepare the quote.', $exception);
        }

        /** @var CustomerInterface|null $customer */
        $customer = $this->shopperContext->getCustomer();
        if (null !== $customer) {
            $this->setCustomerAndAddressOnQuote($quote, $customer);
        }

        $this->quote = $quote;

        return $quote;
    }

    public function reset(): void
    {
        $this->quote = null;
    }

    private function setCustomerAndAddressOnQuote(OrderInterface $quote, CustomerInterface $customer): void
    {
        $this->setCustomer($quote, $customer);

        $defaultAddress = $customer->getDefaultAddress();
        if (null !== $defaultAddress) {
            $clonedAddress = clone $defaultAddress;
            $clonedAddress->setCustomer(null);
            $quote->setBillingAddress($clonedAddress);
        }
    }

    private function setCustomer(OrderInterface $quote, CustomerInterface $customer): void
    {
        $clonedCustomer= clone $customer;
        $clonedCustomer->setUser(null);
        $quote->setCustomer($clonedCustomer);
    }

    public function getCart(): BaseOrderInterface
    {
        return $this->getQuote();
    }
}
