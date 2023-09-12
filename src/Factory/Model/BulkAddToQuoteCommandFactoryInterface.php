<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Factory\Model;

use Asdoria\SyliusQuickShoppingPlugin\Controller\Shop\BulkAddToCartCommandInterface;
use Asdoria\SyliusQuickShoppingPlugin\Factory\Model\BulkAddToCartCommandFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * Class BulkAddToQuoteCommandFactoryInterface
 * @package Asdoria\SyliusQuoteRequestPlugin\Factory\Model
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
interface BulkAddToQuoteCommandFactoryInterface extends BulkAddToCartCommandFactoryInterface
{
    /**
     * @param OrderInterface $quote
     *
     * @return BulkAddToCartCommandInterface
     */
    public function createWithQuote(OrderInterface $quote): BulkAddToCartCommandInterface;
}
