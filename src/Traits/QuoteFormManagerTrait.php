<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Traits;

use Asdoria\SyliusQuoteRequestPlugin\Manager\QuoteFormManagerInterface;

/**
 * Class QuoteFormManagerTrait
 * @package Asdoria\SyliusQuoteRequestPlugin\Traits
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
trait QuoteFormManagerTrait
{
    protected ?QuoteFormManagerInterface $quoteFormManager = null;

    /**
     * @return QuoteFormManagerInterface|null
     */
    public function getQuoteFormManager(): ?QuoteFormManagerInterface
    {
        return $this->quoteFormManager;
    }

    /**
     * @param QuoteFormManagerInterface|null $quoteFormManager
     */
    public function setQuoteFormManager(?QuoteFormManagerInterface $quoteFormManager): void
    {
        $this->quoteFormManager = $quoteFormManager;
    }
}
