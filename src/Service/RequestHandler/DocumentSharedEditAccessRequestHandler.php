<?php

namespace App\Service\RequestHandler;

use App\Entity\DocumentShared;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocumentSharedEditAccessRequestHandler implements RequestHandlerInterface, JsonBodyDecodeInterface
{
    private DocumentShared $documentShared;
    private TranslatorInterface $translator;
    private const NUMBER_OF_POSTED_DATA = 1;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function handleRequest(Request $request): DocumentShared
    {
        $data = $this->decodeJsonBody($request);

        if (count($data) !== self::NUMBER_OF_POSTED_DATA) {
            throw new BadRequestHttpException($this->translator->trans('app.the_data_sent_is_not_correct'));
        }

        return $this->documentShared->setAccess($data['access']);
    }

    public function decodeJsonBody(Request $request): array
    {
        return json_decode($request->getContent(), true);
    }

    public function setDocumentShared(DocumentShared $documentShared): void
    {
        $this->documentShared = $documentShared;
    }
}
