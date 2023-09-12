<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Storage;

use Sylius\Bundle\CoreBundle\Provider\SessionProvider;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class QuoteSessionStorage
 * @package Asdoria\SyliusQuickShoppingPlugin\Storage
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com> */
class QuoteSessionStorage implements QuoteSessionStorageInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private string $sessionKeyName,
        private OrderRepositoryInterface $orderRepository,
    ) {

    }

    public function hasForChannel(ChannelInterface $channel): bool
    {
        try {
            return !empty(SessionProvider::getSession($this->requestStack)->get($this->getQuoteKeyName($channel)));
        } catch (SessionNotFoundException) {
            return false;
        }
    }

    public function getForChannel(ChannelInterface $channel): ?OrderInterface
    {
        if ($this->hasForChannel($channel)) {
            $tokenValue = SessionProvider::getSession($this->requestStack)->get($this->getQuoteKeyName($channel));

            return $this->orderRepository->findOneBy([
                'tokenValue' => $tokenValue,
                'channel' => $channel
            ]);
        }

        return null;
    }

    public function setForChannel(ChannelInterface $channel, OrderInterface $quote): void
    {
        SessionProvider::getSession($this->requestStack)->set($this->getQuoteKeyName($channel), $quote->getTokenValue());
    }

    public function removeForChannel(ChannelInterface $channel): void
    {
        SessionProvider::getSession($this->requestStack)->remove($this->getQuoteKeyName($channel));
    }

    private function getQuoteKeyName(ChannelInterface $channel): string
    {
        return sprintf('%s.%s', $this->sessionKeyName, $channel->getCode());
    }
}
