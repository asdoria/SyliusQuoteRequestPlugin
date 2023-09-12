<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Traits;

use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Class CsrfTokenManagerTrait
 * @package Asdoria\SyliusQuoteRequestPlugin\Traits
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
trait CsrfTokenManagerTrait
{

    protected ?CsrfTokenManagerInterface $csrfTokenManager = null;

    /**
     * @return CsrfTokenManagerInterface|null
     */
    public function getCsrfTokenManager(): ?CsrfTokenManagerInterface
    {
        return $this->csrfTokenManager;
    }

    /**
     * @param CsrfTokenManagerInterface|null $csrfTokenManager
     */
    public function setCsrfTokenManager(?CsrfTokenManagerInterface $csrfTokenManager): void
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }
}
