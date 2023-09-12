<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\TokenAssigner;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * Interface QuoteTokenAssignerInterface
 * @package Asdoria\SyliusQuoteRequestPlugin\TokenAssigner
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
interface QuoteTokenAssignerInterface
{
    public function assignTokenValue(OrderInterface $quote): void;

    public function assignTokenValueIfNotSet(OrderInterface $quote): void;
}
