# digbang/files

This library helps with the handling of files and images.
The files are stored physically using Laravel's filesystem configuration; and logically into a table in the database.  

## Usage

### Mappings
`$builder->manyToOne(File::class, 'propertyName');`
`$builder->belongsTo(ImageFile::class, 'imagePropertyName');`

### Image Handling
Each ImageFile has an optional parent image (the original one). 
This allows to have a tree of curated images attached to the original.

For example:
```php

public function __construct(FileRepository $repository, ImageCurator $curator)
{
    $this->repository = $repository;
    $this->curator = $curator;
}

public function curateImage(ImageFile $originalImage): ImageFile
{
    if (! ($curatedImage = $originalImage->getCurated('SOME_IDENTIFICATION_KEY'))) {
        $originalImage->setContentGetter(function () use ($originalImage) {
            return $this->repository->getContents($originalImage->getId());
        });
    
        $curation = $this->curator
            ->resize(WIDTH, HEIGHT)
            ->interlace()
            ->optimize() //not yet implemented
            ->buildCuration();
    
        $curatedImage = $originalImage->curate('SOME_IDENTIFICATION_KEY', $curation);
        $curatedImage->setContentGetter(function () use ($curatedImage) {
            return $this->repository->getContents($curatedImage->getId());
        });
    
        $this->repository->persist($curatedImage);
    }
    
    return $curatedImage;
}
```

The imageCurator can be extended or replaced. Also, you can implement another curator alongside the pre-existing one and use both at the same time.

The only requirement is that the curator should return an array of callbacks that the Curation object can apply to the image.
