<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Traits;


use Asdoria\SyliusQuoteRequestPlugin\Factory\Model\AddToQuoteCommandFactoryInterface;

/**
 *
 */
trait AddToQuoteCommandFactory
{
    /**
     * @var AddToQuoteCommandFactoryInterface|null
     */
    protected ?AddToQuoteCommandFactoryInterface $addToQuoteCommandFactory = null;

    /**
     * @return AddToQuoteCommandFactoryInterface|null
     */
    public function getAddToQuoteCommandFactory(): ?AddToQuoteCommandFactoryInterface
    {
        return $this->addToQuoteCommandFactory;
    }

    /**
     * @param AddToQuoteCommandFactoryInterface|null $addToQuoteCommandFactory
     */
    public function setAddToQuoteCommandFactory(?AddToQuoteCommandFactoryInterface $addToQuoteCommandFactory): void
    {
        $this->addToQuoteCommandFactory = $addToQuoteCommandFactory;
    }
}
