<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Traits;

use Asdoria\SyliusQuoteRequestPlugin\Context\QuoteContextInterface;

/**
 * Class QuoteContextTrait
 * @package Asdoria\SyliusQuoteRequestPlugin\Traits
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
trait QuoteContextTrait
{
    protected ?QuoteContextInterface $quoteContext = null;

    /**
     * @return QuoteContextInterface|null
     */
    public function getQuoteContext(): ?QuoteContextInterface
    {
        return $this->quoteContext;
    }

    /**
     * @param QuoteContextInterface|null $quoteContext
     */
    public function setQuoteContext(?QuoteContextInterface $quoteContext): void
    {
        $this->quoteContext = $quoteContext;
    }
}
