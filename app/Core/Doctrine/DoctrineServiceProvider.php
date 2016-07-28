<?php

namespace Angelov\Eestec\Platform\Core\Doctrine;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\ServiceProvider;

class DoctrineServiceProvider extends ServiceProvider
{
    public function register()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        $driver = $config->get('database.default');
        $isDevMode = $config->get('app.debug');

        $dbParams = array(
            'driver'   => 'pdo_' . $driver,
            'user'     => $config->get('database.connections.' . $driver . '.username'),
            'password' => $config->get('database.connections.' . $driver . '.password'),
            'dbname'   => $config->get('database.connections.' . $driver . '.database'),
        );

        $paths = [
            app_path() .'/DoctrineExperimental'
        ];

        $cache = new ArrayCache();

        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader, $paths);

        $configuration = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $configuration->setMetadataCacheImpl($cache);
        $configuration->setQueryCacheImpl($cache);
        $configuration->setMetadataDriverImpl($driver);

        $entityManager = EntityManager::create($dbParams, $configuration);

        $this->app->singleton(EntityManager::class, function () use ($entityManager) {
            return $entityManager;
        });
    }
}
