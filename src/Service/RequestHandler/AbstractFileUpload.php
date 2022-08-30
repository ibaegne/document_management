<?php

namespace App\Service\RequestHandler;

use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractFileUpload
{
    public function isFileUpload($data): bool
    {
        return $data instanceof UploadedFile;
    }
}
