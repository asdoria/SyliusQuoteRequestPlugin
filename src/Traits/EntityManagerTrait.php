<?php

declare(strict_types=1);

namespace Asdoria\SyliusQuoteRequestPlugin\Traits;

use Doctrine\ORM\EntityManagerInterface;

/**
 *
 */
trait EntityManagerTrait
{
    protected ?EntityManagerInterface $entityManager = null;

    /**
     * @return EntityManagerInterface|null
     */
    public function getEntityManager(): ?EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManagerInterface|null $entityManager
     */
    public function setEntityManager(?EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}
