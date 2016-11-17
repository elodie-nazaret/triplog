<?php

namespace AppBundle;

use Gedmo\Sluggable\Util;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;

/**
 * Class PictureNamer
 */
class PictureNamer implements NamerInterface
{
    /**
     * {@inheritdoc}
     */
    public function name(FileInterface $file)
    {
        $fileName  = Util\Urlizer::urlize(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

        return sprintf('%s.%s', $fileName, $extension);
    }
}