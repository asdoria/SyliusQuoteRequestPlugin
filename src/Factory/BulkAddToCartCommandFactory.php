<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Factory;

use Asdoria\SyliusQuoteRequestPlugin\Traits\ChannelContextTrait;
use Asdoria\SyliusQuoteRequestPlugin\Traits\EntityManagerTrait;
use Asdoria\SyliusQuoteRequestPlugin\Traits\QuoteSessionStorageTrait;
use Asdoria\SyliusQuickShoppingPlugin\Controller\Shop\BulkAddToCartCommand;
use Asdoria\SyliusQuickShoppingPlugin\Controller\Shop\BulkAddToCartCommandInterface;
use Asdoria\SyliusQuickShoppingPlugin\Factory\Model\BulkAddToCartCommandFactoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class BulkAddToCartCommandFactory
 * @package App\Factory
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class BulkAddToCartCommandFactory implements BulkAddToCartCommandFactoryInterface
{
    use ChannelContextTrait;
    use QuoteSessionStorageTrait;
    use EntityManagerTrait;
    public function __construct(
        protected BulkAddToCartCommandFactoryInterface $bulkAddToCartCommandFactory,
        protected RequestStack $requestStack,
        protected ProductRepositoryInterface $productRepository,
        protected OrderRepositoryInterface $orderRepository,
        protected OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        protected AddToCartCommandFactoryInterface   $addToCartCommandFactory
    ) {

    }

    /**
     * @param int $nbr
     *
     * @return BulkAddToCartCommandInterface
     */
    public function createWithAddToCartItems(int $nbr): BulkAddToCartCommandInterface
    {
        $request    = $this->requestStack->getMainRequest();
        $payload    = $request->request->all('sylius_add_to_cart');
        $productId  = $request->query->get('productId');
        $channel    = $this->channelContext->getChannel();

        $bulkAddToCartCommand = $this->createWithQuote($channel, $nbr, $productId);

        if (empty($productId)) {
            return $bulkAddToCartCommand;
        }

        $item = $bulkAddToCartCommand->getAddToCartCommandItems()->last();
        $cart = $bulkAddToCartCommand->getCart();

        if (!$item instanceof AddToCartCommandInterface) return $bulkAddToCartCommand;

        $product = $this->productRepository->find($productId);

        if (!$product instanceof ProductInterface) return $bulkAddToCartCommand;

        $productVariant = $product->getEnabledVariants()->first();
        if (!$productVariant instanceof ProductVariantInterface) return $bulkAddToCartCommand;

        $exist = $bulkAddToCartCommand->getAddToCartCommandItems()->filter(
            fn(AddToCartCommandInterface $item) => $item->getCartItem()->getVariant() === $productVariant)->first();

        $quantity = 0;
        if ($exist instanceof AddToCartCommandInterface) {
            $bulkAddToCartCommand->getAddToCartCommandItems()->removeElement($item);
            $item     = $exist;
            $quantity = $exist->getCartItem()->getQuantity();
        }

        $cartItem = $item->getCartItem();
        if (!$cartItem instanceof OrderItemInterface) return $bulkAddToCartCommand;

        $cartItem->setVariant($productVariant);

        $quantity += intval($payload['cartItem']['quantity'] ?? 1);

        $this->orderItemQuantityModifier->modify($cartItem, $quantity);

        $cartItem->setOrder($cart);
        $this->entityManager->persist($cartItem);

        return $bulkAddToCartCommand;
    }

    public function createAddToCartCommand($cart): AddToCartCommandInterface
    {
        return $this->bulkAddToCartCommandFactory->createAddToCartCommand($cart);
    }


    public function createWithQuote(ChannelInterface $channel, int $nbr,?string $productId): BulkAddToCartCommandInterface
    {

        if (!$this->getQuoteSessionStorage()->hasForChannel($channel)) {
            $bulk = $this->bulkAddToCartCommandFactory->createWithAddToCartItems($nbr);
            $this->getQuoteSessionStorage()->setForChannel($channel, $bulk->getCart());
            return $bulk;
        }

        $cart = $this->getQuoteSessionStorage()->getForChannel($channel);

        if (!$cart instanceof OrderInterface) {
            return $this->bulkAddToCartCommandFactory->createWithAddToCartItems($nbr);
        }

        $cartItems = new ArrayCollection();
        /** @var OrderItemInterface $row */
        foreach ($cart->getItems() as $row) {
            $cartItems->add($this->addToCartCommandFactory->createWithCartAndCartItem($cart, $row));
        }

        if (!empty($productId)) $cartItems->add($this->createAddToCartCommand($cart));

        return new BulkAddToCartCommand($cart, $cartItems);
    }
}
