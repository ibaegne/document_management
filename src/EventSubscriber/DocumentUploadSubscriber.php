<?php

namespace App\EventSubscriber;

use App\Entity\Document;
use App\Service\FileUploader;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DocumentUploadSubscriber implements EventSubscriberInterface
{
    private FileUploader $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postRemove,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Document) {
            return;
        }

        $this->fileUploader->delete($this->getNameFileWithExtension($entity));
    }

    private function uploadFile($entity)
    {
        if (!$entity instanceof Document) {
            return;
        }

        $file = $entity->getFile();

        if ($file instanceof UploadedFile) {
           $this->fileUploader->upload($file, $this->getNameFileWithExtension($entity));
        }
    }

    private function getNameFileWithExtension(Document $document): string
    {
        return $document->getSafeName() . '.' . $document->getExtension();
    }
}
