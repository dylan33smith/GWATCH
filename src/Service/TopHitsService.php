<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class TopHitsService
{
    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Generate hybrid top hits report based on ranking method
     */
    public function generateHybridTopHitsReport(int $moduleId, string $reportType, int $topHitsCount, string $windowSize = null): array
    {
        try {
            // Extract radius index from window size and get the corresponding radius_ind
            $radiusInd = null;
            if ($windowSize && preg_match('/(\d+)/', $windowSize, $matches)) {
                $radiusVal = (int)$matches[1];
                $radiusInd = $this->getRadiusIndFromValue($moduleId, $radiusVal);
            }
            
            // Step 1: Get top v_inds based on ranking method
            $topVInds = $this->getTopVInds($moduleId, $reportType, $topHitsCount, $radiusInd);
            
            if (empty($topVInds)) {
                return [
                    'success' => false,
                    'error' => 'No data found for the selected criteria',
                    'data' => []
                ];
            }

            // Step 2: Get detailed data for those v_inds
            $data = $this->getDetailedData($moduleId, $topVInds, $reportType, $radiusInd);
            
            return [
                'success' => true,
                'data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Error generating report: ' . $e->getMessage(),
                'debug' => [
                    'moduleId' => $moduleId,
                    'reportType' => $reportType,
                    'topHitsCount' => $topHitsCount,
                    'windowSize' => $windowSize,
                    'radiusInd' => $radiusInd ?? 'not found',
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]
            ];
        }
    }

    /**
     * Get top v_inds based on ranking method
     */
    private function getTopVInds(int $moduleId, string $reportType, int $limit, int $radiusInd = null): array
    {
        $connection = $this->createModuleConnection($moduleId);
        if (!$connection) {
            throw new \Exception('Could not connect to module database');
        }

        try {
            switch ($reportType) {
                case 'P-value':
                    $sql = "SELECT v_ind FROM r_pval ORDER BY r_pval DESC LIMIT {$limit}";
                    break;
                    
                case 'QAS':
                    $sql = "SELECT v_ind FROM r_ratio ORDER BY r_ratio ASC LIMIT {$limit}";
                    break;
                    
                case 'density':
                    if (!$radiusInd) {
                        throw new \Exception('Radius index is required for density ranking');
                    }
                    $sql = "SELECT v_ind FROM top_hits WHERE radius_ind = {$radiusInd} ORDER BY r_density DESC LIMIT {$limit}";
                    break;
                    
                default:
                    throw new \Exception('Invalid report type: ' . $reportType);
            }

            $result = $connection->executeQuery($sql);
            $rows = $result->fetchAllAssociative();
            
            return array_column($rows, 'v_ind');
        } finally {
            $connection->close();
        }
    }

    /**
     * Get detailed data for the top v_inds
     */
    private function getDetailedData(int $moduleId, array $vInds, string $reportType, int $radiusInd = null): array
    {
        if (empty($vInds)) {
            return [];
        }

        $connection = $this->createModuleConnection($moduleId);
        if (!$connection) {
            throw new \Exception('Could not connect to module database');
        }

        try {
            $vIndList = implode(',', $vInds);
            
            $sql = "SELECT 
                        chr.chrname as 'Chromosome',
                        a.alias as 'SNP',
                        p.pos as 'position',
                        c.test as 'test_name',
                        ROUND(-LOG10(pv.pval), 3) as 'neg_log_p',
                        q.ratio as 'QAS',
                        rpv.r_pval as 'ranks_of_neg_log_p',
                        qr.r_ratio as 'ranks_of_QAS',
                        COALESCE(h.r_density, 0) as 'rank_of_density',
                        COALESCE(h.r_naive_p, 0) as 'rank_of_naive_p_value',
                        COALESCE(h.left_ind, 0) as 'left_ind',
                        COALESCE(h.right_ind, 0) as 'right_ind',
                        COALESCE(h.left_cnt + h.right_cnt, 0) as 'number_of_SNPs',
                        COALESCE(h.density, 0) as 'density',
                        ROUND(-LOG10(COALESCE(h.naive_p, pv.pval)), 3) as 'neg_log_naive_p_value'
                    FROM v_ind v
                    JOIN col c ON v.col = c.col
                    JOIN ind i ON v.ind = i.ind
                    JOIN alias a ON v.ind = a.ind
                    JOIN pos p ON v.ind = p.ind
                    JOIN pval pv ON v.v_ind = pv.v_ind
                    JOIN r_pval rpv ON v.v_ind = rpv.v_ind
                    JOIN ratio q ON v.v_ind = q.v_ind
                    JOIN r_ratio qr ON v.v_ind = qr.v_ind
                    JOIN chr chr ON i.chr = chr.chr
                    LEFT JOIN top_hits h ON h.v_ind = v.v_ind AND h.radius_ind = {$radiusInd}
                    WHERE v.v_ind IN ({$vIndList})
                    ORDER BY " . $this->getOrderByClause($reportType);

            $result = $connection->executeQuery($sql);
            $rows = $result->fetchAllAssociative();
            
            return $this->processResults($rows);
        } finally {
            $connection->close();
        }
    }

    /**
     * Get ORDER BY clause based on report type
     */
    private function getOrderByClause(string $reportType): string
    {
        switch ($reportType) {
            case 'P-value':
                return 'rpv.r_pval DESC';
            case 'QAS':
                return 'qr.r_ratio ASC';
            case 'density':
                return 'h.r_density DESC';
            default:
                return 'rpv.r_pval DESC';
        }
    }

    /**
     * Process and format the results
     */
    private function processResults(array $rows): array
    {
        $processed = [];
        
        foreach ($rows as $row) {
            $processed[] = [
                'SNP' => $row['SNP'] ?? 'Unknown',
                'Chromosome' => $row['Chromosome'] ?? 'Unknown',
                'position' => $row['position'] ?? 0,
                'test_name' => $row['test_name'] ?? 'Unknown',
                '-log(p)' => $row['neg_log_p'] ?? 0,
                'ranks_of_-log(p)' => $row['ranks_of_neg_log_p'] ?? 0,
                'QAS' => $row['QAS'] ?? 0,
                'ranks_of_QAS' => $row['ranks_of_QAS'] ?? 0,
                'rank_of_density' => $row['rank_of_density'] ?? 0,
                'rank_of_naive_p_value' => $row['rank_of_naive_p_value'] ?? 0,
                'left_ind' => $row['left_ind'] ?? 0,
                'right_ind' => $row['right_ind'] ?? 0,
                'number_of_SNPs' => $row['number_of_SNPs'] ?? 0,
                'density' => $row['density'] ?? 0,
                '-log(naive_p_value)' => $row['neg_log_naive_p_value'] ?? 0
            ];
        }
        
        return $processed;
    }

    /**
     * Get radius_ind from radius_val
     */
    private function getRadiusIndFromValue(int $moduleId, int $radiusVal): ?int
    {
        $connection = $this->createModuleConnection($moduleId);
        if (!$connection) {
            return null;
        }

        try {
            $sql = "SELECT radius_ind FROM radius_ind WHERE radius_val = {$radiusVal} LIMIT 1";
            $result = $connection->executeQuery($sql);
            $row = $result->fetchAssociative();
            
            return $row ? (int)$row['radius_ind'] : null;
        } catch (\Exception $e) {
            return null;
        } finally {
            $connection->close();
        }
    }

    /**
     * Create connection to module database
     */
    private function createModuleConnection(int $moduleId): ?Connection
    {
        try {
            $dbName = "Module_{$moduleId}";
            $baseUrl = $this->params->get('app.database_url');
            $urlParts = parse_url($baseUrl);
            $urlParts['path'] = '/' . $dbName;
            
            $moduleUrl = $this->buildUrl($urlParts);
            return DriverManager::getConnection(['url' => $moduleUrl]);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Build URL from parts
     */
    private function buildUrl(array $parts): string
    {
        $url = '';
        
        if (isset($parts['scheme'])) {
            $url .= $parts['scheme'] . '://';
        }
        
        if (isset($parts['user'])) {
            $url .= $parts['user'];
            if (isset($parts['pass'])) {
                $url .= ':' . $parts['pass'];
            }
            $url .= '@';
        }
        
        if (isset($parts['host'])) {
            $url .= $parts['host'];
            if (isset($parts['port'])) {
                $url .= ':' . $parts['port'];
            }
        }
        
        if (isset($parts['path'])) {
            $url .= $parts['path'];
        }
        
        if (isset($parts['query'])) {
            $url .= '?' . $parts['query'];
        }
        
        return $url;
    }
}
