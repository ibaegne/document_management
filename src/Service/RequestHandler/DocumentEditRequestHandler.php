<?php

namespace App\Service\RequestHandler;

use App\Entity\Document;
use App\Service\FileInfos;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocumentEditRequestHandler extends AbstractFileUpload implements RequestHandlerInterface
{
    private Document $document;
    private FileInfos $fileInfos;
    private TranslatorInterface $translator;

    public function __construct(FileInfos $fileInfos, TranslatorInterface $translator)
    {
        $this->fileInfos = $fileInfos;
        $this->translator = $translator;
    }

    public function handleRequest(Request $request): Document
    {
        $documentFile = $request->files->get('file');

        if (!$this->isFileUpload($documentFile)) {
            $errorMessage = $this->translator->trans('app.the_file_to_import_is_mandatory');
            $this->logger->error($errorMessage);
            throw new BadRequestHttpException($errorMessage);
        }

        $this->fileInfos->setFile($documentFile);
        $this->document->setName($this->fileInfos->getOriginalName());
        $this->document->setExtension($this->fileInfos->getExtension());
        $this->document->setFile($documentFile);

        return $this->document;
    }

    public function setDocument(Document $document): void
    {
        $this->document = $document;
    }
}
