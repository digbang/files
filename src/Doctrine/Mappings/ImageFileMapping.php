<?php

namespace Digbang\Files\Doctrine\Mappings;

use Digbang\Files\ImageFile;
use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;

class ImageFileMapping extends EntityMapping
{
    /**
     * Returns the fully qualified name of the class that this mapper maps.
     *
     * @return string
     */
    public function mapFor()
    {
        return ImageFile::class;
    }

    /**
     * Load the object's metadata through the Metadata Builder object.
     *
     * @param Fluent $builder
     */
    public function map(Fluent $builder)
    {
        $builder->text('curationKey')->nullable();

        $builder->manyToOne(ImageFile::class, 'original')->nullable();
        $builder->oneToMany(ImageFile::class, 'curated')->mappedBy('original');
    }
}
