<?php
declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Twig;

use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class QuoteExtension
 * @package Asdoria\SyliusQuoteRequestPlugin\Twig
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class QuoteExtension extends AbstractExtension
{
    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(protected OrderRepositoryInterface $orderRepository)
    {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('get_quote_by_token_value', [$this, 'getQuote']),
        ];
    }

    /**
     * @param string $tokenValue
     *
     * @return OrderInterface|null
     */
    public function getQuote(string $tokenValue): ?OrderInterface
    {
        return $this->orderRepository->findOneByTokenValue($tokenValue);
    }
}
