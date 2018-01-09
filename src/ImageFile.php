<?php

namespace Digbang\Files;

use Doctrine\Common\Collections\ArrayCollection;

class ImageFile extends File
{
    /** @var string|null */
    private $curationKey;

    /** @var ImageFile|null */
    private $original;

    /** @var ImageFile[]|ArrayCollection */
    private $curated;

    public function __construct(string $path, string $filename, string $originalName, string $mime)
    {
        parent::__construct($path, $filename, $originalName, $mime);

        $this->curated = new ArrayCollection();
    }

    /**
     * @return null|string
     */
    public function getCurationKey()
    {
        return $this->curationKey;
    }

    /**
     * @return Imagefile|array
     */
    public function getCurated(?string $curationKey)
    {
        if($curationKey) {
            return $this->curated->filter(function(ImageFile $item) use($curationKey) {
                return $item->getCurationKey() == $curationKey;
            })->first();
        }

        return $this->curated->getValues();
    }

    public function curate(string $curationKey, Curation $curation)
    {
        $entity = new static($this->getPath(), '', $this->getOriginalName(), $this->getMime());

        $entity->filename = (string)$entity->id;
        $entity->extension = $this->getExtension();

        $entity->contents = $curation->apply($this->getContents());

        $entity->curationKey = $curationKey;
        $entity->original = $this;

        $this->curated->add($entity);

        return $entity;
    }
}
