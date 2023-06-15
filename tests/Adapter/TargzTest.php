<?php

namespace Compression\Adapter;

use Compression\BaseTest;
use Compression\Contract\CompressionAdapterInterface;
use Compression\Contract\ExecutorInterface;

class TargzTest extends BaseTest
{
    use CompressionAwareTrait;

    /**
     * @var \Compression\Adapter\Targz
     */
    private $targz;

    protected function setUp(): void
    {
        $this->targz = new Targz();
    }

    public function testTargzIsInstantiatedAndIsValidAdapter()
    {
        $this->assertInstanceOf(CompressionAdapterInterface::class, $this->targz);
    }

    public function testIsCompressed()
    {
        $result = $this->targz->isCompressed(self::FIXTURES . '/archive.tar.gz');

        $this->assertTrue($result);
    }

    public function testIsCompressedFalse()
    {
        $result = $this->targz->isCompressed(self::FIXTURES . '/file.txt');

        $this->assertFalse($result);
    }

    public function testTargzCompressesGivenFile()
    {
        $result = $this->targz->compress(self::FIXTURES . '/file.txt', self::FIXTURES . '/file.tar.gz');

        $this->assertTrue(true, $result);
        $this->assertFileExists(self::FIXTURES . '/file.tar.gz');
    }

    public function testTargzDecompressesGivenFile()
    {
        mkdir(self::TMPDIR . '/targz');
        $result = $this->targz->decompress(self::FIXTURES . '/archive.tar.gz', self::TMPDIR . '/targz/');

        $this->assertTrue(true, $result);
        $this->assertFileExists(self::TMPDIR . '/targz/file.txt');

        // Cleanup
        $this->deleteRecursive(self::TMPDIR . '/targz');
    }

    public function testTargzThrowsExceptionWhenTryToCompressNonExistingFile()
    {
        $this->expectException(\Compression\Exception\TargzException::class);
        $this->expectExceptionMessage("Targz: unable to compress, somefile.txt not available.");
        $this->targz->compress(self::FIXTURES . '/somefile.txt', self::FIXTURES . '/somefile.tar.gz');
    }

    public function testTargzThrowsExceptionWhenTryToDecompressesNonCompressedFile()
    {
        $this->expectException(\Compression\Exception\TargzException::class);
        $this->expectExceptionMessage("Unable to decompress, file.txt is not a valid tar archive.");
        $this->targz->decompress(self::FIXTURES . '/file.txt', self::FIXTURES . '/targz/');
    }

    public function testTargzCompressThrowsExceptionWhenFailedInternally()
    {
        $this->expectException(\Compression\Exception\TargzException::class);
        $this->expectExceptionMessage("Tar: Unable to compress source file.txt.");
        $executorMock = $this->getMockBuilder(ExecutorInterface::class)
            ->setMethods(['execute'])
            ->getMock();
        $executorMock
            ->method('execute')
            ->willReturn(5);

        $targz = new Targz($executorMock);
        $targz->compress(
            self::FIXTURES . '/file.txt',
            self::FIXTURES . '/targz/'
        );
    }

    public function testTargzDecompressThrowsExceptionWhenFailedInternally()
    {
        $this->expectException(\Compression\Exception\TargzException::class);
        $this->expectExceptionMessage("Tar: Unable to decompress file archive.tar.gz.");
        $executorMock = $this->getMockBuilder(ExecutorInterface::class)
            ->setMethods(['execute'])
            ->getMock();
        $executorMock
            ->method('execute')
            ->willReturn(5);

        $targz = new Targz($executorMock);
        $targz->decompress(
            self::FIXTURES . '/archive.tar.gz',
            self::FIXTURES . '/targz/'
        );
    }

    public function testPigzThrowsExceptionWhenTryToCompressDirectory()
    {
        $this->expectException(\Compression\Exception\TargzException::class);
        $this->expectExceptionMessage("Directory compression is not yet supported.");
        $this
            ->targz
            ->compress(
                self::FIXTURES,
                self::FIXTURES . '/compressed.tar.gz'
            );
    }

    public function tearDown(): void
    {
        @unlink(self::FIXTURES . '/file.tar.gz');
        @unlink(self::FIXTURES . '/somefile.tar.gz');
        @unlink(self::FIXTURES . '/compressed.tar.gz');
    }
}
