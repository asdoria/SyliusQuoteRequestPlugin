<?php


declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Context;

use Asdoria\SyliusQuoteRequestPlugin\Context\QuoteContextInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * Class QuoteContext
 * @package Sylius\Component\Order\Context
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
final class QuoteContext implements QuoteContextInterface
{
    public function __construct(private FactoryInterface $cartFactory)
    {
    }

    public function getQuote(): OrderInterface
    {
        return $this->cartFactory->createNew();
    }

    public function getCart(): BaseOrderInterface
    {
        return $this->getQuote();
    }
}
