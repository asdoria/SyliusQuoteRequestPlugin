<?php

declare(strict_types=1);


namespace Asdoria\SyliusQuoteRequestPlugin\Factory;


use Asdoria\SyliusQuoteRequestPlugin\Controller\Shop\AddToQuoteCommand;
use Asdoria\SyliusQuoteRequestPlugin\Controller\Shop\AddToQuoteCommandInterface;
use Asdoria\SyliusQuoteRequestPlugin\Factory\Model\AddToQuoteCommandFactoryInterface;
use Asdoria\SyliusQuoteRequestPlugin\Traits\QuoteContextTrait;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

/**
 * Class AddToCartCommandFactory
 * @package Asdoria\SyliusQuickShoppingPlugin\Factory
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class AddToQuoteCommandFactory implements AddToQuoteCommandFactoryInterface
{

    public function createWithCartAndCartItem(OrderInterface $cart, OrderItemInterface $cartItem): AddToCartCommandInterface
    {
        return $this->createWithQuoteAndCartItem($cart, $cartItem);
    }

    public function createWithQuoteAndCartItem(OrderInterface $quote, OrderItemInterface $cartItem): AddToQuoteCommandInterface
    {
        return new AddToQuoteCommand($quote, $cartItem);
    }
}
