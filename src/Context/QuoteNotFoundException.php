<?php


declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Context;

/**
 * Class QuoteNotFoundException
 * @package Asdoria\SyliusQuoteRequestPlugin\Context
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class QuoteNotFoundException extends \RuntimeException
{
    public function __construct(?string $message = null, ?\Exception $previousException = null)
    {
        parent::__construct($message ?? 'Sylius was not able to figure out the current quote.', 0, $previousException);
    }
}
