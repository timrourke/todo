<?php


namespace App\Tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\HttpKernel\KernelInterface;

trait BootstrapsSqliteTestDatabaseTrait
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    protected function bootstrapSqliteTestDatabase(KernelInterface $kernel)
    {
        $this->connection = $kernel
            ->getContainer()
            ->get('doctrine.dbal.default_connection');

        /**
         * @var \Doctrine\ORM\EntityManager $entityManager
         */
        $entityManager = $kernel
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        /**
         * Run the schema update tool using our entity metadata
         *
         * @var \Doctrine\ORM\Mapping\ClassMetadata[] $metadatas
         */
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metadatas);
    }
}