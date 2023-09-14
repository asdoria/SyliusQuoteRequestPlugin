<?php

declare(strict_types=1);


namespace Asdoria\SyliusQuoteRequestPlugin\Manager;

use Symfony\Component\Form\FormInterface;

/**
 * Class QuoteFormManagerInterface
 * @package Asdoria\SyliusQuoteRequestPlugin\Manager
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
interface QuoteFormManagerInterface
{
    public function manage(
        FormInterface $form,
        array $validationGroups
    ): void;
}
