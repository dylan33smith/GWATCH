<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DatabaseConfigurationService
{
    private array $config;
    
    public function __construct(ParameterBagInterface $params)
    {
        // Parse the DATABASE_URL from .env properly
        $databaseUrl = $params->get('app.database_url');
        $this->config = $this->parseDatabaseUrl($databaseUrl);
    }
    
    public function getMainDatabaseConfig(): array
    {
        return $this->config;
    }
    
    public function getModuleDatabaseConfig(string $moduleName): array
    {
        $config = $this->config;
        $config['dbname'] = $moduleName;
        return $config;
    }
    
    private function parseDatabaseUrl(string $url): array
    {
        $parts = parse_url($url);
        
        // Parse query string for additional parameters
        $query = [];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }
        
        return [
            'driver' => 'pdo_mysql',
            'host' => $parts['host'] ?? '127.0.0.1',
            'port' => $parts['port'] ?? 3306,
            'user' => $parts['user'] ?? '',
            'password' => $parts['pass'] ?? '',
            'dbname' => trim($parts['path'] ?? '', '/'),
            'serverVersion' => $query['serverVersion'] ?? '8.0.42',
            'charset' => $query['charset'] ?? 'utf8mb4'
        ];
    }
}
