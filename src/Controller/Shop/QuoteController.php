<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Shop\Controller\Shop;

use Asdoria\SyliusQuickShoppingPlugin\Controller\Shop\BulkAddToCartCommandInterface;
use Asdoria\SyliusQuickShoppingPlugin\Controller\Shop\QuickShoppingController;
use Asdoria\SyliusQuickShoppingPlugin\Form\Type\BulkAddToCartType;
use Asdoria\SyliusQuoteRequestPlugin\Traits\ChannelContextTrait;
use Asdoria\SyliusQuoteRequestPlugin\Traits\QuoteSessionStorageTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


/**
 * Class QuickShoppingController
 * @package App\Controller
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class QuoteController extends QuickShoppingController
{
    use QuoteSessionStorageTrait;
    use ChannelContextTrait;
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
        $bulkAddToCartCommand = $this->bulkAddToCartCommandFactory->createWithAddToCartItems(1);
        $form = $this->formFactory->create(
            BulkAddToCartType::class,
            $bulkAddToCartCommand
        );

        $channel = $this->channelContext->getChannel();
        if(!$this->getQuoteSessionStorage()->hasForChannel($channel)) {
            $this->getQuoteSessionStorage()->setForChannel($channel, $bulkAddToCartCommand->getCart());
        }

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid() && $form->isSubmitted()) {
            /** @var BulkAddToCartCommandInterface $bulkAddToCartCommand */
            $bulkAddToCartCommand = $form->getData();
            $cart = $bulkAddToCartCommand->getCart();
            $cart->getItems()->clear();
            foreach ($form->get('cartItems') as $childForm) {
                $addToCartCommand = $childForm->getData();
                $this->orderModifier->addToOrder($cart, $addToCartCommand->getCartItem());
            }
            $cart->setState(OrderInterface::STATE_QUOTE);
            $this->cartManager->persist($cart);
            $this->cartManager->flush();

            return new JsonResponse(
                ['redirect' => $this->urlGenerator->generate('sylius_shop_contact_request')]
            );
        }

        $this->cartManager->persist($bulkAddToCartCommand->getCart());
        $this->cartManager->flush();

        return new Response(
            $this->twig->render('@AsdoriaSyliusQuickShoppingPlugin/Shop/QuickShopping/quote.html.twig',
                [
                    'form' => $form->createView(),
                    'errors' => $form->getErrors(true, true)
                ]
            )
        );
    }
}
