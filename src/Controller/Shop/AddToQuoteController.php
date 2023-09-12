<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Controller\Shop;


use Asdoria\SyliusQuoteRequestPlugin\Form\Type\AddToQuoteType;
use Asdoria\SyliusQuoteRequestPlugin\Traits\AddToQuoteCommandFactory;
use Asdoria\SyliusQuoteRequestPlugin\Traits\CsrfTokenManagerTrait;
use Asdoria\SyliusQuoteRequestPlugin\Traits\QuoteContextTrait;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\ConfigurableViewHandlerInterface;
use FOS\RestBundle\View\View;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AddToQuoteController
 * @package Asdoria\SyliusQuoteRequestPlugin\Controller\Shop
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class AddToQuoteController
{
    use AddToQuoteCommandFactory;
    use QuoteContextTrait;
    use CsrfTokenManagerTrait;

    public function __construct(
        protected CartItemFactoryInterface           $quoteItemFactory,
        protected ProductRepositoryInterface         $productRepository,
        protected OrderItemQuantityModifierInterface $quantityModifier,
        protected OrderModifierInterface             $orderModifier,
        protected FormFactoryInterface               $formFactory,
        protected EntityManagerInterface             $quoteManager,
        protected ConfigurableViewHandlerInterface   $restViewHandler,
        protected ValidatorInterface                 $validator,
        protected array                              $validationGroups
    )
    {
    }

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
        $productId = $request->query->get('productId');

        if (empty($productId)) {
            throw new \InvalidArgumentException('productId is required for add the ccartitem to the quote');
        }

        $product = $this->productRepository->find($productId);

        if (!$product instanceof ProductInterface) {
            throw new \InvalidArgumentException('product is not found');
        }

        $quote = $this->getQuoteContext()->getQuote();

        /** @var OrderItemInterface $orderItem */
        $orderItem = $this->quoteItemFactory->createForProduct($product);

        $this->quantityModifier->modify($orderItem, 1);

        $addToQuoteCommand = $this->addToQuoteCommandFactory->createWithQuoteAndCartItem($quote, $orderItem);

        $form = $this->formFactory->create(
            AddToQuoteType::class,
            $addToQuoteCommand,
            ['product' => $product, 'validation_groups' => $this->validationGroups]
        );

        $payload = $request->request->all('asdoria_add_to_quote');

        if (!$this->isCsrfTokenValid('asdoria-shop-quote-request-add-to-quote', $payload['_token'] ?? null)) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isSubmitted()) {
            /** @var AddToQuoteCommandInterface $addToQuoteCommand */
            $addToQuoteCommand = $form->getData();

            $this->orderModifier->addToOrder($addToQuoteCommand->getQuote(), $addToQuoteCommand->getCartItem());
            $this->quoteManager->persist($quote);
            $this->quoteManager->flush();
            
            /** @var FlashBagInterface $flashBag */
            $flashBag = $request->getSession()->getBag('flashes');
            $flashBag->add('success', 'asdoria_quote_request.ui.shop.success_add_to_quote');

            return new JsonResponse('success add to quote');
        }

        $form = $this->getAddToCartFormWithErrors($this->getCartItemErrors($orderItem), $form);

        return $this->restViewHandler($this->createView($form));
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


    protected function getCartItemErrors(OrderItemInterface $orderItem): ConstraintViolationListInterface
    {
        return $this->validator
            ->validate($orderItem, null, $this->validationGroups);
    }


    /**
     * @param View $view
     *
     * @return Response
     */
    public function restViewHandler(View $view): Response
    {
        $this->restViewHandler->setExclusionStrategyGroups(['sylius', 'Default', 'Details']);
        $this->restViewHandler->setExclusionStrategyVersion('2.0.0');

        $view->getContext()->enableMaxDepth();
        $view->setFormat('json');
        return $this->restViewHandler->handle($view);
    }


    /**
     * @param FormInterface $form
     *
     * @return View
     */
    protected function createView(FormInterface $form):View {
        $errors = $form->getErrors(true);

        return $errors->count() > 0 ?
            View::create($form, Response::HTTP_BAD_REQUEST)->setData(['errors' => $errors]):
            View::create($form, Response::HTTP_OK)
                ->setData([]);
    }

    /**
     * Checks the validity of a CSRF token.
     *
     * @param string      $id    The id used when generating the token
     * @param string|null $token The actual token sent with the request that should be validated
     *
     * @final
     */
    protected function isCsrfTokenValid(string $id, ?string $token): bool
    {
        if (empty($this->getCsrfTokenManager())) {
            throw new \LogicException('CSRF protection is not enabled in your application. Enable it with the "csrf_protection" key in "config/packages/framework.yaml".');
        }

        return $this->getCsrfTokenManager()->isTokenValid(new CsrfToken($id, $token));
    }
}
