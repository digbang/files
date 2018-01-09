<?php

namespace Digbang\Files;

use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class File
{
    /** @var \Ramsey\Uuid\UuidInterface */
    protected $id;
    /** @var string */
    protected $path;
    /** @var string */
    protected $mime;
    /** @var string */
    protected $filename;
    /** @var string */
    protected $originalName;
    /** @var string */
    protected $extension;
    /** @var \SplFileInfo */
    protected $contents;
    /** @var callable */
    protected $contentGetter;

    public function __construct(string $path, string $filename, string $originalName, string $mime)
    {
        $this->id = Uuid::uuid4();
        $this->path = $path;
        $this->mime = $mime;
        $this->originalName = $originalName;
        $this->filename = $filename;
    }

    public static function fromHttpFile(\Illuminate\Http\File $file, string $path = '', ?string $filename = '')
    {
        $entity = new static($path, $filename, $file->getFilename(), $file->getMimeType());
        $entity->extension = $file->getExtension();
        $entity->contents = $file;

        $entity->filename = (string)$entity->id;

        return $entity;
    }

    public static function fromUploadedFile(UploadedFile $file, string $path = '', ?string $filename = '')
    {
        $entity = new static($path, $filename, $file->getClientOriginalName(), $file->getMimeType());
        $entity->extension = $file->getClientOriginalExtension();
        $entity->contents = $file;

        $entity->filename = (string)$entity->id;

        return $entity;
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        return ltrim("{$this->path}/{$this->filename}", '\\/');
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    public function getContents()
    {
        if(!$this->contents && $this->contentGetter) {
            $getter = $this->contentGetter;
            $this->contents = $getter();
        }

        return $this->contents;
    }

    public function setContentGetter(callable $callback): void
    {
        $this->contentGetter = $callback;
    }
}
