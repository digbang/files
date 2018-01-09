<?php

namespace Digbang\Files\Doctrine\Repositories;

use Digbang\Files\File;
use Digbang\Files\FileRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Illuminate\Filesystem\FilesystemManager;
use Intervention\Image\Image as InterventionImage;
use Ramsey\Uuid\UuidInterface;

class DoctrineFileRepository extends EntityRepository implements FileRepository
{
    /** @var FilesystemManager */
    private $filesystem;

    public function __construct(EntityManager $entityManager, FilesystemManager $filesystem)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata(File::class));

        $this->filesystem = $filesystem;
    }

    public function getContents(UuidInterface $id)
    {
        return $this->filesystem->disk()->get($id->toString());
    }

    public function persist(File $file)
    {
        $this->_em->transactional(function () use ($file) {
            $contents = $file->getContents();
            if ($contents instanceof \Symfony\Component\HttpFoundation\File\File) {
                $this->filesystem->putFileAs($file->getPath(), $contents, $file->getFilename());
            } elseif ($contents instanceof InterventionImage) {
                $this->filesystem->put($file->getPath() . '/' . $file->getFilename(), $contents->encode()->getEncoded());
            } else {
                $this->filesystem->put($file->getPath() . '/' . $file->getFilename(), $contents);
            }

            $this->_em->persist($file);
            $this->_em->flush($file);
        });
    }

    public function get(UuidInterface $id)
    {
        /** @var File $entity */
        $entity = $this->find($id);
        if ($entity) {
            $entity->setContentGetter(function () use ($id) {
                return $this->getContents($id);
            });

            return $entity;
        }

        throw new EntityNotFoundException(static::CLASS_NAME);
    }
}
