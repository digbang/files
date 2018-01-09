<?php

namespace Digbang\Files;

use Ramsey\Uuid\UuidInterface;

interface FileRepository
{
    const CLASS_NAME = File::class;

    public function persist(File $file);

    public function get(UuidInterface $id);

    public function getContents(UuidInterface $id);
}
