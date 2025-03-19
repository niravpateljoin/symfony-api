<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class PropertyService
{
    private CacheInterface $cache;
    private LoggerInterface $logger;
    private string $projectDir;

    public function __construct(CacheInterface $cache, LoggerInterface $logger, KernelInterface $kernel)
    {
        $this->cache = $cache;
        $this->logger = $logger;
        $this->projectDir = $kernel->getProjectDir();
    }

    public function getProperties(): array
    {
        return $this->cache->get('properties_data', function (ItemInterface $item): array {
            $item->expiresAfter(3600);
            $this->logger->info('Cache MISS: Reloading data...');
            return $this->loadAndNormalizeData();
        });
    }

    private function loadAndNormalizeData(): array
    {
        $dataDirectory = $this->projectDir . '/public/data';

        $files = glob($dataDirectory . '/*.json');

        $properties = [];

        foreach ($files as $file) {
            $data = $this->loadJsonFile($file);
            if ($data === null) {
                continue;
            }
            foreach ($data as $item) {
                $properties[] = [
                    'id'      => $item['id'] ?? ($item['location'] ?? uniqid()),
                    'address' => $item['address'] ?? $item['location'] ?? 'Unknown',
                    'price'   => $item['price'] ?? $item['cost'] ?? 0,
                    'source'  => $item['source'] ?? basename($file),
                ];
            }
        }

        return $properties;
    }

    private function loadJsonFile(string $file): ?array
    {
        if (!file_exists($file)) {
            $this->logger->error("Missing file: {$file}");
            return null;
        }

        $content = file_get_contents($file);
        $data = json_decode($content, true);

        if (!is_array($data)) {
            $this->logger->error("Invalid JSON data in file: {$file}");
            return null;
        }

        return $data;
    }

    public function clearCache(): void
    {
        $this->cache->delete('properties_data');
    }
}
