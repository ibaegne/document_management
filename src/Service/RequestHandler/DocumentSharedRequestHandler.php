<?php

namespace App\Service\RequestHandler;

use App\Entity\Document;
use App\Entity\DocumentShared;
use App\Entity\User;
use App\Exception\AccessValueNotFoundException;
use App\Exception\DocumentNotFoundException;
use App\Exception\UserNotFoundException;
use App\Repository\DocumentRepository;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocumentSharedRequestHandler implements RequestHandlerInterface, JsonBodyDecodeInterface
{
    private LoggerInterface $logger;
    private DocumentRepository $documentRepository;
    private const NUMBER_OF_POSTED_DATA = 3;
    private const ACCESS = [DocumentShared::ACCESS_READ, DocumentShared::ACESS_EDITOR];
    private UserRepository $userRepository;
    private TranslatorInterface $translator;

    public function __construct(
        LoggerInterface $logger,
        DocumentRepository $documentRepository,
        UserRepository $userRepository,
        TranslatorInterface $translator
    ) {
        $this->logger = $logger;
        $this->documentRepository = $documentRepository;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
    }

    public function handleRequest(Request $request): DocumentShared
    {
        $data = $this->decodeJsonBody($request);

        if (count($data) !== self::NUMBER_OF_POSTED_DATA) {
            throw new BadRequestHttpException($this->translator->trans('app.the_data_sent_is_not_correct'));
        }
        $documentShared = $this->createInstance();

        $document = $this->getDocument($data['document']);
        $access = $data['access'];

        if(!$document instanceof Document) {
            throw new DocumentNotFoundException($this->translator->trans('app.the_document_to_share_does_not_exist'));
        }

        if(!in_array($access, self::ACCESS))  {
            throw new AccessValueNotFoundException(
                $this->translator->trans('app.the_right_does_not_exist', ['right' => $access])
            );
        }

        $documentShared->setDocument($document);
        $documentShared->setAccess($access);

        foreach ($data['users'] as $userInfos) {
            $user = $this->getUser($userInfos['email']);

            if(!$user instanceof User) {
                throw new UserNotFoundException(
                    $this->translator->trans('app.the_user_does_not_exist', ['name' => $userInfos['name']])
                );
            }

            $documentShared->addReceiver($user);
        }

        return $documentShared;
    }

    public function decodeJsonBody(Request $request): array
    {
        return json_decode($request->getContent(), true);
    }

    private function createInstance(): DocumentShared
    {
        return new DocumentShared();
    }

    private function getDocument(string $safeName): ?Document
    {
        return $this->documentRepository->findOneBy(compact('safeName'));
    }

    private function getUser(string $email): ?User
    {
        return $this->userRepository->findOneBy(compact('email'));
    }
}
