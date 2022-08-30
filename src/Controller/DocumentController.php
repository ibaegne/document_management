<?php

namespace App\Controller;

use App\Entity\Document;
use App\Exception\DocumentAlreadyExistsException;
use App\Exception\DocumentNotFoundException;
use App\Repository\DocumentRepository;
use App\Security\Voter\DocumentVoter;
use App\Service\RequestHandler\DocumentEditRequestHandler;
use App\Service\RequestHandler\DocumentRemoveRequestHandler;
use App\Service\RequestHandler\DocumentImportRequestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}/app/document", name="app_document_")
 */
class DocumentController extends AbstractController
{
    private DocumentRepository $documentRepository;
    private TranslatorInterface $translator;

    public function __construct(DocumentRepository $documentRepository, TranslatorInterface $translator)
    {
        $this->documentRepository = $documentRepository;
        $this->translator = $translator;
    }

    /**
     * @Route("/list", name="list", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        return $this->json($this->documentRepository->findAllOwnerWithShared($this->getUser()));
    }

    /**
     * @Route("/import", name="import", methods={"GET", "POST"})
     * @return JsonResponse|Response
     * @throws \Exception
     */
    public function import(
        Request                      $request,
        DocumentImportRequestHandler $documentRequestHandler
    ) {
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            try {
                $this->documentRepository->add($documentRequestHandler->handleRequest($request), true);

                return $this->json($this->translator->trans('app.the_document_has_been_imported'));
            } catch (BadRequestHttpException | DocumentAlreadyExistsException $exception) {
                return $this->json($exception->getMessage(), Response::HTTP_BAD_REQUEST);
            }
        }

        return $this->renderForm('document/import.html.twig');
    }

    /**
     * @Route("/{name}/edit", name="edit", methods={"POST"})
     * @ParamConverter("document", options={"mapping": {"name": "safeName"}})
     */
    public function edit(
        Request $request,
        Document $document,
        DocumentEditRequestHandler $documentEditRequestHandler
    ): JsonResponse {
        try {
            $this->denyAccessUnlessGranted(DocumentVoter::EDIT, $document);
            $documentEditRequestHandler->setDocument($document);
            $documentEditRequestHandler->handleRequest($request);

            $this->documentRepository->execute();

            return $this->json($this->translator->trans('app.modified_document'));

        }catch (BadRequestHttpException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     *  @Route("/{name}/download", name="download", methods={"GET"})
     * @ParamConverter("document", options={"mapping": {"name": "safeName"}})
     */
    public function download(
        Document $document,
        ParameterBagInterface $parameterBag
    ): BinaryFileResponse {

        $this->denyAccessUnlessGranted(DocumentVoter::DOWNLOAD, $document);
        $fileNameUpload =  $document->getSafeName() . '.' . $document->getExtension();

        return $this->file($parameterBag->get(
            'documents_directory') . '/' . $fileNameUpload,
            $document->getName()
        );
    }

    /**
     * @Route("/remove", name="remove", methods={"POST"})
     */
    public function remove(
        Request $request,
        DocumentRemoveRequestHandler $documentRemoveRequestHandler
    ): JsonResponse {
        try {
            $document = $documentRemoveRequestHandler->handleRequest($request);
            $this->denyAccessUnlessGranted(DocumentVoter::REMOVE, $document);
            $this->documentRepository->remove($document, true);

            return $this->json($this->translator->trans('app.the_document_has_been_deleted'));

        }catch (BadRequestHttpException | DocumentNotFoundException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
