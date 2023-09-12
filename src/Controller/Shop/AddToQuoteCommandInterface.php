<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Controller\Shop;

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Order\Model\OrderInterface;

/**
 * Class AddToQuoteCommandInterface
 * @package Asdoria\SyliusQuoteRequestPlugin\Controller\Shop
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
interface AddToQuoteCommandInterface extends AddToCartCommandInterface
{
    public function getQuote(): OrderInterface;
}
