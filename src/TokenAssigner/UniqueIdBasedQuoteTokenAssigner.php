<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\TokenAssigner;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;

/**
 * Class UniqueIdBasedQuoteTokenAssigner
 * @package Asdoria\SyliusQuoteRequestPlugin\TokenAssigner
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
final class UniqueIdBasedQuoteTokenAssigner implements QuoteTokenAssignerInterface
{
    public function __construct(private RandomnessGeneratorInterface $generator)
    {
    }

    public function assignTokenValue(OrderInterface $quote): void
    {
        $quote->setTokenValue(sprintf('Q-%s', $this->generator->generateUriSafeString(10)));
    }

    public function assignTokenValueIfNotSet(OrderInterface $quote): void
    {
        if (null === $quote->getTokenValue()) {
            $this->assignTokenValue($quote);
        }
    }
}
