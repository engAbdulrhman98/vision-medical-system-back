<?php

namespace App\Support\PathGenerator;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Illuminate\Support\Str;

class CustomPathGenerator implements PathGenerator
{
    /**
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    /**
     * Get the path for conversions of the given media, relative to the root storage path.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    /**
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive-images/';
    }

    /**
     * Get a unique base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
        $modelType = $media->model_type;
        $folderName = 'others';

        if ($modelType) {
            $classBaseName = class_basename($modelType);
            $folderName = strtolower(Str::plural(Str::kebab($classBaseName)));
        }

        return $folderName . '/' . $media->id;
    }
}
