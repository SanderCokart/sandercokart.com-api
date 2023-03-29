<?php

namespace App\Services\MediaLibrary;

use App\Enums\Disk;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;
use URL;

class UrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        if ($this->media->disk === Disk::privateArticles->name) {
            return $this->getPrivateUrl();
        }

        return parent::getUrl();
    }

    /**
     * Get the private URL for the media.
     *
     * Returns a signed URL that never expires.
     * The url contains the media id and the disk name.
     * The disk name is hashed with SHA1 to prevent the user from guessing the disk name.
     *
     * @return string
     */
    protected function getPrivateUrl(): string
    {
        return URL::signedRoute('media.show', [
            'uuid' => $this->media->uuid,
            'disk' => sha1($this->media->disk),
        ]);
    }
}
