<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileInfos
{
    private SluggerInterface $slugger;
    private UploadedFile $file;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function getOriginalName(string $fileName = null): string
    {
        return pathinfo($fileName ?? $this->file->getClientOriginalName(), PATHINFO_FILENAME);
    }

     public function getSafeFileName(): string
     {
         return strtolower($this->transformOriginalName() . $this->getUniqid());
     }

    public function getExtension(): string
    {
        return strtolower($this->file->getClientOriginalExtension());
    }

     public function setFile(UploadedFile $file)
     {
         $this->file = $file;
     }

     public function transformOriginalName()
     {
         $this->slugger->slug($this->getOriginalName());
     }
     public function getUniqid(): string
     {
         return uniqid();
     }
}
