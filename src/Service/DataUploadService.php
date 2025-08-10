<?php

namespace App\Service;

use App\Entity\Gwatch\ModuleTracking;
use App\Entity\Module\Chr;
use App\Entity\Module\ChrSupp;
use App\Entity\Module\Col;
use App\Entity\Module\Ind;
use App\Entity\Module\RPval;
use App\Entity\Module\RRatio;
use App\Entity\Module\VInd;
use App\Entity\Module\Pos;
use App\Entity\Module\Alias;
use App\Entity\Module\Allele;
use App\Entity\Module\Maf;
use App\Entity\Module\Pval;
use App\Entity\Module\Ratio;
use App\Service\DatabaseConfigurationService;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class DataUploadService
{
    private EntityManagerInterface $entityManager;
    private SluggerInterface $slugger;
    private string $uploadDir;
    private DatabaseConfigurationService $databaseConfig;

    public function __construct(
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        DatabaseConfigurationService $databaseConfig,
        string $uploadDir = '%kernel.project_root%/data/uploads'
    ) {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
        $this->databaseConfig = $databaseConfig;
        $this->uploadDir = $uploadDir;
    }
    
    public function getUploadDir(): string
    {
        return $this->uploadDir;
    }

    // TODO: Add data upload logic here step by step for testing
}
