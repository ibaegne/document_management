<?php

namespace App\Tests\Unit\Service;

use App\Service\FileInfos;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileInfosTest extends TestCase
{
    public function testGetOriginalName()
    {
        $fileInfos = new FileInfos($this->createMock(SluggerInterface::class));

        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->expects($this->once())
            ->method('getClientOriginalName')
            ->willReturn('document.pdf');

        $fileInfos->setFile($uploadedFile);

        $this->assertEquals('document', $fileInfos->getOriginalName());
        $this->assertEquals('document', $fileInfos->getOriginalName('document.pdf'));
        $this->assertEquals('document', $fileInfos->getOriginalName('document'));
    }

    public function testGetSafeFileName()
    {
        $fileInfos = $this->createPartialMock(FileInfos::class, ['getUniqid', 'transformOriginalName']);
        $fileInfos->expects($this->once())
            ->method('getUniqid')
            ->willReturn('1234ed')
            ;
        $fileInfos->expects($this->once())
            ->method('transformOriginalName')
            ->willReturn('Attestation_de_regularite_fiscale')
            ;

        $this->assertEquals('attestation_de_regularite_fiscale1234ed', $fileInfos->getSafeFileName());
    }

    public function testGetExtension()
    {
        $fileInfos = new FileInfos($this->createMock(SluggerInterface::class));

        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->expects($this->once())
            ->method('getClientOriginalExtension')
            ->willReturn('pdf');

        $fileInfos->setFile($uploadedFile);

        $this->assertEquals('pdf', $fileInfos->getExtension());
    }
}
