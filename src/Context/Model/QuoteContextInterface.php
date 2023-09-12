<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Context\Model;

use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface;

/**
 * Class QuoteContextInterface
 *
 * @author Philippe Vesin <pve.asdoria@gmail.com>
 */
interface QuoteContextInterface extends CartContextInterface
{
    public function getQuote(): OrderInterface;
}
