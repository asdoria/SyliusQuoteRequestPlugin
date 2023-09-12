<?php

declare(strict_types=1);


namespace Asdoria\SyliusQuoteRequestPlugin\Factory\Model;

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

/**
 * Class AddToQuoteCommandFactoryInterface
 *
 * @author Philippe Vesin <pve.asdoria@gmail.com>
 */
interface AddToQuoteCommandFactoryInterface extends AddToCartCommandFactoryInterface
{
    public function createWithQuoteAndCartItem(OrderInterface $cart, OrderItemInterface $cartItem): AddToCartCommandInterface;
}
