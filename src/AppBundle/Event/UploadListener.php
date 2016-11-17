<?php

namespace AppBundle\Event;

use Gedmo\Sluggable\Util;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Oneup\UploaderBundle\Uploader\File\GaufretteFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UploadListener
 */
class UploadListener
{
    /**
     * @param PostPersistEvent $event
     */
    public function onUpload(PostPersistEvent $event)
    {
        /** @var GaufretteFile $file */
        $file      = $event->getFile();
        $filesName = [];

        $fileName     = Util\Urlizer::urlize(pathinfo($file->getName(), PATHINFO_FILENAME));
        $extension    = pathinfo($file->getName(), PATHINFO_EXTENSION);
        $fileNameSlug = sprintf('%s.%s', $fileName, $extension);

        $absolutePath = sprintf('%s'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'%s', getcwd(), $fileNameSlug);

        if (exif_imagetype($absolutePath) == IMAGETYPE_JPEG) {
            $this->fixImageOrientation($absolutePath);
        }

        $filesName[] = $fileNameSlug;

        $response = $event->getResponse();
        $response['filesName'] = $filesName;
    }

    /**
     * @param string $filename
     */
    private function fixImageOrientation($filename)
    {
        $exif = exif_read_data($filename);

        if (!empty($exif['Orientation'])) {
            $image = imagecreatefromjpeg($filename);

            switch ($exif['Orientation']) {
                case 3:
                    $image = imagerotate($image, 180, 0);
                    break;

                case 6:
                    $image = imagerotate($image, -90, 0);
                    break;

                case 8:
                    $image = imagerotate($image, 90, 0);
                    break;
            }

            imagejpeg($image, $filename, 90);
        }
    }
}