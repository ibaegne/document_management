<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private ParameterBagInterface $parameterBag;
    private LoggerInterface $logger;

    public function __construct(
        ParameterBagInterface $parameterBag,
        LoggerInterface $logger
    ) {
        $this->parameterBag = $parameterBag;
        $this->logger = $logger;
    }

    public function upload(UploadedFile $file, string $fileName)
    {
        try {
            $file->move($this->getTargetDirectory(), $fileName);

            return true ;
        } catch (FileException $e) {
            $this->logger->error($e->getMessage());
        }

        return false;
    }

    public function delete(string $fileName): void
    {
        $filesystem = new Filesystem();
        $path = $this->getTargetDirectory() . '/' . $fileName;

        $filesystem->remove($path);
    }

    public function getTargetDirectory()
    {
        return $this->parameterBag->get('documents_directory');
    }
}
