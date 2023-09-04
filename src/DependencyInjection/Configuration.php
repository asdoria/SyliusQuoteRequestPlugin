<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Asdoria\SyliusQuoteRequestPlugin\DependencyInjection
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com> */
final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('asdoria_quote_request');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
