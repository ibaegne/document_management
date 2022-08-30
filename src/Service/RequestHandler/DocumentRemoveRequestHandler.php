<?php

namespace App\Service\RequestHandler;

use App\Entity\Document;
use App\Exception\DocumentNotFoundException;
use App\Repository\DocumentRepository;
use App\Service\FileInfos;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocumentRemoveRequestHandler implements RequestHandlerInterface, JsonBodyDecodeInterface
{
    private Security $security;
    private LoggerInterface$logger;
    private FileInfos $fileInfos;
    private DocumentRepository $documentRepository;
    private TranslatorInterface $translator;

    public function __construct(
        FileInfos $fileInfos,
        Security $security,
        LoggerInterface  $logger,
        DocumentRepository $documentRepository,
        TranslatorInterface $translator
    ) {
        $this->fileInfos = $fileInfos;
        $this->security = $security;
        $this->logger = $logger;
        $this->documentRepository = $documentRepository;
        $this->translator = $translator;
    }

    public function handleRequest(Request $request): Document
    {
        $data = $this->decodeJsonBody($request) ?? null;
        $documentName = $data['documentName'] ?? null;

        if (!$documentName) {
            throw new BadRequestHttpException($this->translator->trans('app.enter_the_name_of_the_document_to_be_deleted'));
        }

        $document = $this->getDocument($documentName);

        if(!$document instanceof Document) {
            throw new DocumentNotFoundException($this->translator->trans('app.the_document_does_not_exist', ['name' => $documentName]));
        }

        return $document;
    }

    public function decodeJsonBody(Request $request): array
    {
        return json_decode($request->getContent(), true);
    }

    private function getDocument(string $name): ?Document
    {
        return $this->documentRepository->findOneBy(['safeName' => $name]);
    }
}
