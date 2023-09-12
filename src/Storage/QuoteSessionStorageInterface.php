<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Storage;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * Interface QuoteSessionStorageInterface
 * @package Asdoria\SyliusQuoteRequestPlugin\Storage
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
interface QuoteSessionStorageInterface
{
    public function hasForChannel(ChannelInterface $channel): bool;

    public function getForChannel(ChannelInterface $channel): ?OrderInterface;

    public function setForChannel(ChannelInterface $channel, OrderInterface $cart): void;

    public function removeForChannel(ChannelInterface $channel): void;
}
