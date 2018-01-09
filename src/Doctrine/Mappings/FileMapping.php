<?php

namespace Digbang\Files\Doctrine\Mappings;

use Digbang\Files\Doctrine\Types\UuidType;
use Digbang\Files\File;
use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;

class FileMapping extends EntityMapping
{
    /**
     * Returns the fully qualified name of the class that this mapper maps.
     *
     * @return string
     */
    public function mapFor()
    {
        return File::class;
    }

    /**
     * Load the object's metadata through the Metadata Builder object.
     *
     * @param Fluent $builder
     */
    public function map(Fluent $builder)
    {
        $builder->field(UuidType::NAME, 'id')->primary();
        $builder->singleTableInheritance()->column('type');

        $builder->text('path');
        $builder->text('filename');
        $builder->text('originalName');
        $builder->string('mime');
        $builder->string('extension');
    }
}
