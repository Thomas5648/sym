<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class Powitanie
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    public function Powit(string $name): string
    {
        $this->logger->info("Siema $name"); 
        return "Siema $name";
    }
}