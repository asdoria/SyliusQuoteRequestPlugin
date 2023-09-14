<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Manager;

use Asdoria\SyliusQuickShoppingPlugin\Controller\Shop\BulkAddToCartCommandInterface;
use Asdoria\SyliusQuoteRequestPlugin\Controller\Shop\AddToQuoteCommandInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Class QuoteManager
 *
 * @author Philippe Vesin <pve.asdoria@gmail.com>
 */
class QuoteFormManager implements QuoteFormManagerInterface
{

    public function __construct(
        protected OrderModifierInterface $orderModifier,
        protected ValidatorInterface $validator
    )
    {
    }

    public function manage(
        FormInterface $form,
        array $validationGroups
    ): void {

        if (!$form->has('cartItems')) return;

        $bulkAddToQuoteCommand = $form->getData();

        if (!$bulkAddToQuoteCommand instanceof BulkAddToCartCommandInterface) return;

        $quote = $bulkAddToQuoteCommand->getCart();

        foreach ($quote->getItems() as $orderItem) {
            $this->orderModifier->removeFromOrder($quote, $orderItem);
        }

        foreach ($form->get('cartItems') as $childForm) {
            /** @var AddToQuoteCommandInterface $addToQuoteCommand */
            $addToQuoteCommand = $childForm->getData();
            $errors            = $this->getCartItemErrors($addToQuoteCommand->getCartItem(), $validationGroups);
            if (0 < count($errors)) {
                $this->getAddToCartFormWithErrors($errors, $childForm);
            }
            $this->orderModifier->addToOrder($addToQuoteCommand->getCart(), $addToQuoteCommand->getCartItem());
        }
    }


    protected function getCartItemErrors(OrderItemInterface $orderItem, array $validationGroups): ConstraintViolationListInterface
    {
        return $this->validator
            ->validate($orderItem, null, $validationGroups)
            ;
    }

    protected function getAddToCartFormWithErrors(ConstraintViolationListInterface $errors, FormInterface $form): FormInterface
    {
        foreach ($errors as $error) {
            $formSelected = empty($error->getPropertyPath())
                ? $form->get('cartItem')
                : $form->get('cartItem')->get($error->getPropertyPath());

            $formSelected->addError(new FormError($error->getMessage()));
        }

        return $form;
    }
}
