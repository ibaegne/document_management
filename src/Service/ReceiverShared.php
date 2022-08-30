<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\DocumentShared;

class ReceiverShared
{
    public function getReceiversAndInfosDocumentShared(Document $document): array
    {
        $receivers =  [];

        foreach ($document->getDocumentsShared() as $documentShared) {
            $receivers = $this->getData($documentShared, $receivers);
        }

        return $receivers;
    }

    private function getData(DocumentShared $documentShared, array $receivers): array
    {
        foreach ($documentShared->getReceivers() as $receiver) {
            if($receiver->isIsEnabled()) {
                $receiver = [
                    'receiver' => $receiver->getName(),
                    'access' => $documentShared->getAccess(),
                    'id' => $documentShared->getId()
                ];

                array_push($receivers, $receiver);
            }
        }

        return $receivers;
    }
}
