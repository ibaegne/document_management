<?php

namespace App\Security\Voter;

use App\Entity\Document;
use App\Entity\DocumentShared;
use App\Entity\User;
use App\Repository\DocumentSharedRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class DocumentVoter extends Voter
{
    public const EDIT = 'edit';
    public const REMOVE = 'remove';
    public const DOWNLOAD = 'download';
    public const SHARED = 'shared';

    /**
     * @var DocumentSharedRepository
     */
    private $documentSharedRepository;

    public function __construct(DocumentSharedRepository $documentSharedRepository)
    {
        $this->documentSharedRepository = $documentSharedRepository;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::REMOVE, self::DOWNLOAD, self::SHARED])
            && $subject instanceof Document;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return  $this->canEdit($subject, $user);
                break;
            case self::REMOVE:
            case self::SHARED:
                return  $this->canRemove($subject, $user);
                break;
            case self::DOWNLOAD:
                return  $this->canDownload($subject, $user);
                break;
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Document $document, User $user): bool
    {
        return $user === $document->getOwner() || $this->isSharedDocumentWithEditAccess($document, $user);
    }

    private function canDownload(Document $document, User $user): bool
    {
        return $user === $document->getOwner()
            || $this->isSharedDocumentWithReadAccess($document, $user)
            || $this->isSharedDocumentWithEditAccess($document, $user)
        ;
    }

    private function canRemove(Document $document, User $user): bool
    {
        return $user === $document->getOwner();
    }

    private function isSharedDocumentWithEditAccess(Document $document, User $user): bool
    {
        $documentShared = $this->documentSharedRepository->findOneByDocumentAndReceiver($document, $user);

        if(!$documentShared instanceof DocumentShared) {
            return false;
        }

        return $documentShared->getAccess() === DocumentShared::ACESS_EDITOR;
    }

    private function isSharedDocumentWithReadAccess(Document $document, User $user): bool
    {
        $documentShared = $this->documentSharedRepository->findOneByDocumentAndReceiver($document, $user);

        if(!$documentShared instanceof DocumentShared) {
            return false;
        }

        return $documentShared->getAccess() === DocumentShared::ACCESS_READ;
    }

}
