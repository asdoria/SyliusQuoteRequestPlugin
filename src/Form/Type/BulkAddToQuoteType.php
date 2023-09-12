<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Form\Type;

use Asdoria\SyliusQuickShoppingPlugin\Form\Type\BulkAddToCartItemType;
use Asdoria\SyliusQuickShoppingPlugin\Form\Type\BulkAddToCartType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class BulkAddToQuoteType
 * @package Asdoria\SyliusQuoteRequestPlugin\Form\Type
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class BulkAddToQuoteType extends BulkAddToCartType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cartItems', CollectionType::class, [
                'entry_type' => BulkAddToQuoteItemType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_options' =>  [
                    'empty_data' => function (FormInterface $form) {
                        return $this->bulkAddToCartCommandFactory->createAddToCartCommand($this->cartContext->getCart());
                    }
                ]
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'asdoria_quote_request_bulk_add_to_quote';
    }
}
