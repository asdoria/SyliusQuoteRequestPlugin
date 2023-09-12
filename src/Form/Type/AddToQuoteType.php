<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Form\Type;

use Sylius\Bundle\OrderBundle\Form\Type\CartItemType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantMatchType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AddToQuoteType
 *
 * @author Philippe Vesin <pve.asdoria@gmail.com>
 */
class AddToQuoteType  extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('cartItem', CartItemType::class, ['product' => $options['product']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefined([
                'product',
            ])
            ->setAllowedTypes('product', ProductInterface::class)
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'asdoria_add_to_quote';
    }
}
