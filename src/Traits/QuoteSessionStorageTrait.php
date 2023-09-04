<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Traits;

use Asdoria\SyliusQuoteRequestPlugin\Storage\QuoteSessionStorageInterface;

/**
 * Class QuoteSessionStorageTrait
 * @package App\Traits
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
trait QuoteSessionStorageTrait
{
    protected ?QuoteSessionStorageInterface $quoteSessionStorage = null;

    /**
     * @return QuoteSessionStorageInterface|null
     */
    public function getQuoteSessionStorage(): ?QuoteSessionStorageInterface
    {
        return $this->quoteSessionStorage;
    }

    /**
     * @param QuoteSessionStorageInterface|null $quoteSessionStorage
     */
    public function setQuoteSessionStorage(?QuoteSessionStorageInterface $quoteSessionStorage): void
    {
        $this->quoteSessionStorage = $quoteSessionStorage;
    }
}
