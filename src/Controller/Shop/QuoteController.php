<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Controller\Shop;

use Asdoria\SyliusQuickShoppingPlugin\Controller\Shop\BulkAddToCartCommandInterface;
use Asdoria\SyliusQuickShoppingPlugin\Controller\Shop\QuickShoppingController;
use Asdoria\SyliusQuoteRequestPlugin\Factory\Model\BulkAddToQuoteCommandFactoryInterface;
use Asdoria\SyliusQuoteRequestPlugin\Form\Type\BulkAddToQuoteType;
use Asdoria\SyliusQuoteRequestPlugin\Traits\QuoteContextTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


/**
 * Class QuoteController
 * @package Asdoria\SyliusQuoteRequestPlugin\Controller\Shop
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class QuoteController extends QuickShoppingController
{
    use QuoteContextTrait;

    /**
     * @param Request $request
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(Request $request): Response
    {
        if (!$this->bulkAddToCartCommandFactory instanceof BulkAddToQuoteCommandFactoryInterface) {
            throw new \InvalidArgumentException('Invalid service bulkAddToQuoteCommandFactory');
        }

        $quote = $this->getQuoteContext()->getQuote();

        $bulkAddToQuoteCommand = $this->bulkAddToCartCommandFactory->createWithQuote($quote);

        $form = $this->formFactory->create(
            BulkAddToQuoteType::class,
            $bulkAddToQuoteCommand
        );

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid() && $form->isSubmitted()) {
            /** @var BulkAddToCartCommandInterface $bulkAddToQuoteCommand */
            $bulkAddToQuoteCommand = $form->getData();
            $quote                 = $bulkAddToQuoteCommand->getCart();

            foreach ($quote->getItems() as $orderItem) {
                $this->orderModifier->removeFromOrder($quote, $orderItem);
            }

            foreach ($form->get('cartItems') as $childForm) {
                $addToQuoteCommand = $childForm->getData();
                $errors            = $this->getCartItemErrors($addToQuoteCommand->getCartItem());
                if (0 < count($errors)) {
                    $this->getAddToCartFormWithErrors($errors, $childForm);
                }
                $this->orderModifier->addToOrder($addToQuoteCommand->getCart(), $addToQuoteCommand->getCartItem());
            }

            if ($form->getErrors(true, true)->count() == 0) {
                $this->cartManager->persist($quote);
                $this->cartManager->flush();

                /** @var FlashBagInterface $flashBag */
                $flashBag = $request->getSession()->getBag('flashes');
                $flashBag->add('success', 'asdoria_quote_request.ui.shop.success_save_my_quote');

                if ($request->query->has('_redirect')) {
                    return new RedirectResponse($request->query->get('_redirect'));
                }
            }
        }

        return new Response(
            $this->twig->render('@AsdoriaSyliusQuoteRequestPlugin/Shop/Quote/index.html.twig',
                [
                    'form'   => $form->createView(),
                    'errors' => $form->getErrors(true, true)
                ]
            )
        );
    }
}
