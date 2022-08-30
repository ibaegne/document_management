<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\DocumentShared;
use App\Exception\AccessValueNotFoundException;
use App\Exception\DocumentNotFoundException;
use App\Exception\UserNotFoundException;
use App\Repository\DocumentSharedRepository;
use App\Security\Voter\DocumentVoter;
use App\Service\ReceiverShared;
use App\Service\RequestHandler\DocumentSharedEditAccessRequestHandler;
use App\Service\RequestHandler\DocumentSharedRequestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/{_locale}/app/document/shared", name="app_document_shared")
 */
class DocumentSharedController extends AbstractController
{
    private DocumentSharedRepository $documentSharedRepository;

    public function __construct(DocumentSharedRepository $documentSharedRepository)
    {
        $this->documentSharedRepository = $documentSharedRepository;
    }

    /**
     * @Route("/list", name="_list", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        return $this->json($this->documentSharedRepository->findAllReceiver($this->getUser()));
    }

    /**
     * @Route("", methods={"POST"})
     */
    public function shared(
        Request $request,
        DocumentSharedRequestHandler $documentSharedRequestHandler
    ): JsonResponse {
        try {
            $documentShared = $documentSharedRequestHandler->handleRequest($request);
            $this->denyAccessUnlessGranted(DocumentVoter::SHARED, $documentShared->getDocument());

            $this->documentSharedRepository->add(
                $documentSharedRequestHandler->handleRequest($request),
                true
            );

            return $this->json('Le partage a bien été réalisé');
        }catch (UserNotFoundException | DocumentNotFoundException | AccessValueNotFoundException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

     /**
      * @Route("/{name}/receivers", name="_receivers", methods={"GET"})
      * @ParamConverter("document", options={"mapping": {"name": "safeName"}})
      */
    public function receiver(Document $document, ReceiverShared $receiverShared): JsonResponse
    {
        $this->denyAccessUnlessGranted(DocumentVoter::SHARED, $document);

        return $this->json($receiverShared->getReceiversAndInfosDocumentShared($document));
    }
    /**
     * @Route("/{id}/access-edit", name="access_edit", methods={"POST"})
     */
    public function editAccess(
        Request $request,
        DocumentShared $documentShared,
        DocumentSharedEditAccessRequestHandler $documentSharedEditAccessRequestHandler
    ): JsonResponse {
        $this->denyAccessUnlessGranted(DocumentVoter::SHARED, $documentShared->getDocument());

        $documentSharedEditAccessRequestHandler->setDocumentShared($documentShared);
        $documentSharedEditAccessRequestHandler->handleRequest($request);

        $this->documentSharedRepository->execute();

        return $this->json('Modification éffcetuée');
    }
}
