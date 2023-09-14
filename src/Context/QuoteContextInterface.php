<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Context;

use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface;

/**
 * Interface QuoteContextInterface
 * @package Asdoria\SyliusQuoteRequestPlugin\Context
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
interface QuoteContextInterface extends CartContextInterface
{
    public function getQuote(): OrderInterface;
}
