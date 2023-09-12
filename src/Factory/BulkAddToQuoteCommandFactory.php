<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Factory;

use Asdoria\SyliusQuickShoppingPlugin\Controller\Shop\BulkAddToCartCommand;
use Asdoria\SyliusQuickShoppingPlugin\Controller\Shop\BulkAddToCartCommandInterface;
use Asdoria\SyliusQuickShoppingPlugin\Factory\Model\BulkAddToCartCommandFactoryInterface;
use Asdoria\SyliusQuoteRequestPlugin\Factory\Model\BulkAddToQuoteCommandFactoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

/**
 * Class BulkAddToQuoteCommandFactory
 * @package Asdoria\SyliusQuoteRequestPlugin\Factory
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class BulkAddToQuoteCommandFactory implements BulkAddToQuoteCommandFactoryInterface
{
    /**
     * @param BulkAddToCartCommandFactoryInterface $inner
     * @param AddToCartCommandFactoryInterface $addToCartCommandFactory
     */
    public function __construct(
        protected BulkAddToCartCommandFactoryInterface $inner,
        protected AddToCartCommandFactoryInterface $addToCartCommandFactory
    ) {
    }

    /**
     * @param int $nbr
     *
     * @return BulkAddToCartCommandInterface
     */
    public function createWithAddToCartItems(int $nbr): BulkAddToCartCommandInterface {
        return $this->inner->createWithAddToCartItems($nbr);
    }

    /**
     * @param $cart
     *
     * @return AddToCartCommandInterface
     */
    public function createAddToCartCommand($cart): AddToCartCommandInterface {
        return $this->inner->createAddToCartCommand($cart);
    }

    /**
     * @param OrderInterface $quote
     *
     * @return BulkAddToCartCommandInterface
     */
    public function createWithQuote(OrderInterface $quote): BulkAddToCartCommandInterface
    {
        $quoteItems = new ArrayCollection();
        /** @var OrderItemInterface $row */
        foreach ($quote->getItems() as $row) {
            $quoteItems->add($this->addToCartCommandFactory->createWithCartAndCartItem($quote, $row));
        }

        return new BulkAddToCartCommand($quote, $quoteItems);
    }
}
