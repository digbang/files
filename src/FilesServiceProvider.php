<?php

namespace Digbang\Files;

use Digbang\Files\Doctrine\Repositories\DoctrineFileRepository;
use Digbang\Files\Doctrine\Types\InterventionImageType;
use Digbang\Files\Doctrine\Types\UuidType;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Facades\Image as ImageFacade;
use Intervention\Image\ImageServiceProvider;
use LaravelDoctrine\Fluent\FluentDriver;
use LaravelDoctrine\ORM\Configuration\MetaData\MetaDataManager;
use LaravelDoctrine\ORM\Extensions\MappingDriverChain;

class FilesServiceProvider extends ServiceProvider
{
    private const PACKAGE = 'files';

    public function boot(EntityManagerInterface $entityManager, MetaDataManager $metadata)
    {
        $this->doctrineMappings($entityManager, $metadata);
        $this->resources();
    }

    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), static::PACKAGE);

        $this->app->bind(FileRepository::class, DoctrineFileRepository::class);

        $this->registerTypes();

        $this->registerInterventionImage();
    }

    protected function doctrineMappings(EntityManagerInterface $entityManager, MetaDataManager $metadata): void
    {
        /** @var FluentDriver $fluentDriver */
        $fluentDriver = $metadata->driver('fluent', [
            'mappings' => [
                Doctrine\Mappings\FileMapping::class,
                Doctrine\Mappings\ImageFileMapping::class,
            ],
        ]);

        /** @var MappingDriverChain $chain */
        $chain = $entityManager->getConfiguration()->getMetadataDriverImpl();
        $chain->addDriver($fluentDriver, 'Digbang\Files');
    }

    protected function resources(): void
    {
        $this->publishes([
            $this->configPath() => config_path(static::PACKAGE . '.php'),
        ],
        'config');
    }

    private function configPath(): string
    {
        return dirname(__DIR__) . '/config/files.php';
    }

    private function registerTypes()
    {
        if (! Type::hasType(InterventionImageType::NAME)) {
            Type::addType(InterventionImageType::NAME, InterventionImageType::class);
        }

        if (! Type::hasType(UuidType::NAME)) {
            Type::addType(UuidType::NAME, UuidType::class);
        }
    }

    private function registerInterventionImage()
    {
        $this->app->register(ImageServiceProvider::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('Image', ImageFacade::class);
    }
}
