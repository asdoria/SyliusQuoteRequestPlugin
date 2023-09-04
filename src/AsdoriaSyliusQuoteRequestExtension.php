<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AsdoriaSyliusQuoteRequestExtension extends Bundle
{
    use SyliusPluginTrait;

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
