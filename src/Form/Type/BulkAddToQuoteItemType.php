<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Form\Type;

use Asdoria\SyliusQuickShoppingPlugin\Form\Type\BulkAddToCartItemType;

/**
 * Class BulkAddToCartItemType
 * @package Asdoria\SyliusQuickShoppingPlugin\Form\Type
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
final class BulkAddToQuoteItemType extends BulkAddToCartItemType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'asdoria_quick_shopping_bulk_add_to_cart_item';
    }
}
