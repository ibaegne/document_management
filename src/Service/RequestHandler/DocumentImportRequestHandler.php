<?php

namespace App\Service\RequestHandler;

use App\Entity\Document;
use App\Exception\DocumentAlreadyExistsException;
use App\Service\FileInfos;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocumentImportRequestHandler extends AbstractFileUpload implements RequestHandlerInterface
{
    private Security $security;
    private LoggerInterface$logger;
    private FileInfos $fileInfos;
    private ValidatorInterface $validator;
    private TranslatorInterface $translator;

    public function __construct(
        FileInfos $fileInfos,
        Security $security,
        ValidatorInterface $validator,
        LoggerInterface  $logger,
        TranslatorInterface  $translator
    ) {
        $this->fileInfos = $fileInfos;
        $this->security = $security;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    public function handleRequest(Request $request): Document
    {
        $documentFile = $request->files->get('file');

        if (!$this->isFileUpload($documentFile)) {
            $errorMessage = $this->translator->trans('app.the_file_to_import_is_mandatory');;
            $this->logger->error($errorMessage);
            throw new BadRequestHttpException($errorMessage);
        }

        $document = $this->createInstance();

        $document->setOwner($this->security->getUser());
        $this->fileInfos->setFile($documentFile);
        $document->setName($this->fileInfos->getOriginalName());
        $document->setSafeName($this->fileInfos->getSafeFileName());
        $document->setExtension($this->fileInfos->getExtension());
        $document->setFile($documentFile);

        $errors = $this->validator->validate($document);

        if (count($errors) > 0) {
            $errorMessage = $this->translator->trans('app.you_already_added_a_document_with_this_name');
            $this->logger->error($errorMessage);
            throw new DocumentAlreadyExistsException($errorMessage);
        }

        return $document;
    }

    private function createInstance(): Document
    {
       return new Document();
    }
}
