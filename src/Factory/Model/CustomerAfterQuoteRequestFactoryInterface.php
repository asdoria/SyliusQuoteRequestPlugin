<?php


declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Factory\Model;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface CustomerAfterQuoteRequestFactoryInterface
{
    public function createAfterQuoteRequest(OrderInterface $order, array $data): ?CustomerInterface;
}
