<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $publicUploadsDirectory;

    public function __construct($publicUploadsDirectory)
    {
        $this->publicUploadsDirectory = $publicUploadsDirectory;
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->transliterate($originalFilename);
        $fileName = $safeFilename.'-'.uniqid('', true).'.'.$file->guessExtension();

        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->publicUploadsDirectory;
    }

    /**
     * Transliterate the filename for upload.
     *
     * @param string $filename filename to transliterate in latin.
     *
     * @return string
     */
    public function transliterate(string $filename)
    {
        return transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $filename);
    }
}
