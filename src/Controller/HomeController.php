<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}/app", name="app_")
 */
class HomeController extends AbstractController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("", name="my_documents")
     */
    public function myDocuments(DocumentRepository $documentRepository): Response
    {
        return $this->render('home/documents.html.twig', [
            'title' => $this->translator->trans('app.my_documents'),
            'path' => $this->generateUrl('app_document_list')
        ]);
    }

    /**
     * @Route("/shared-with-me", name="shared_with_me")
     */
    public function sharedWithMe(): Response
    {
        return $this->render('home/documents.html.twig', [
            'title' => $this->translator->trans('app.shared_with_me'),
            'path' => $this->generateUrl('app_document_shared_list')
        ]);
    }
}
