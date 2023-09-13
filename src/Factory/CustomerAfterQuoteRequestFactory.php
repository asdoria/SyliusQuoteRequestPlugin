<?php


declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Factory;

use Asdoria\SyliusQuoteRequestPlugin\Factory\Model\CustomerAfterQuoteRequestFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;

final class CustomerAfterQuoteRequestFactory implements CustomerAfterQuoteRequestFactoryInterface
{
    public function __construct(
        private RepositoryInterface $customerRepository,
        private FactoryInterface $customerFactory,
        private CanonicalizerInterface $canonicalizer,
    )
    {
    }

    /**
     * @param OrderInterface $order
     * @param array          $data
     *
     * @return CustomerInterface|null
     */
    public function createAfterQuoteRequest(OrderInterface $order, array $data): ?CustomerInterface
    {
        if (!isset($data['email'])) {
            return null;
        }

        $emailCanonical = $this->canonicalizer->canonicalize($data['email']);

        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['emailCanonical' => $emailCanonical]);

        // assign existing customer or create a new one
        if (null === $customer) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($data['email']);
            $address = $order->getBillingAddress();
            if ($address instanceof AddressInterface) {
                $customer->setFirstName($address->getFirstName());
                $customer->setLastName($address->getLastName());
                $customer->setPhoneNumber($address->getPhoneNumber());
            }
        }

        return $customer;
    }
}
