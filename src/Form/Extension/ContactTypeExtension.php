<?php
declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Form\Extension;

use Asdoria\SyliusQuoteRequestPlugin\Traits\QuoteContextTrait;
use Sylius\Bundle\CoreBundle\Form\Type\ContactType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactTypeExtension extends AbstractTypeExtension
{
    use QuoteContextTrait;

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->getQuoteContext()->getQuote()->isEmpty()) return;

        $builder
            ->add('quoteTokenValue', HiddenType::class, [
                'required' => false,
            ]);
    }

    /**
     * @return iterable
     */
    public static function getExtendedTypes(): iterable
    {
        return [
            ContactType::class,
        ];
    }
}
