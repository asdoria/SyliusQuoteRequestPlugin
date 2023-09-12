<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Controller\Shop;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

final class AddToQuoteCommand implements AddToQuoteCommandInterface
{
    public function __construct(private OrderInterface $quote, private OrderItemInterface $cartItem)
    {
    }

    public function getCart(): OrderInterface
    {
        return $this->getQuote();
    }

    public function getCartItem(): OrderItemInterface
    {
        return $this->cartItem;
    }

    public function getQuote(): OrderInterface
    {
        return $this->quote;
    }

}
