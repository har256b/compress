<?php

namespace Compression\Adapter;

use Compression\BaseTest;
use Compression\Contract\CompressionAdapterInterface;
use Compression\Contract\ExecutorInterface;

class ZipTest extends BaseTest
{
    use CompressionAwareTrait;

    /**
     * @var \Compression\Adapter\Zip
     */
    private $zip;

    protected function setUp(): void
    {
        $this->zip = new Zip();
    }

    public function testPigzIsInstantiatedAndIsValidAdapter()
    {
        $this->assertInstanceOf(CompressionAdapterInterface::class, $this->zip);
    }

    public function testIsCompressed()
    {
        $result = $this
            ->zip
            ->isCompressed(
                self::FIXTURES . '/fixture.zip'
            );

        $this->assertTrue($result);
    }

    public function testIsCompressedFalse()
    {
        $result = $this
            ->zip
            ->isCompressed(
                self::FIXTURES . '/file.txt'
            );

        $this->assertFalse($result);
    }

    public function testZipCompressesGivenFile()
    {
        $result = $this
            ->zip
            ->compress(
                self::FIXTURES . '/file.txt',
                self::FIXTURES . '/file.zip'
            );

        $this->assertTrue(true, $result);
        $this->assertFileExists(self::FIXTURES . '/file.zip');
    }

    public function testZipThrowsExceptionWhenCompressNotExistingFile()
    {
        $this->expectException(\Compression\Exception\ZipException::class);
        $this->expectExceptionMessage("Zip: unable to compress, somefile.txt not available.");
        $this
            ->zip
            ->compress(
                self::FIXTURES . '/somefile.txt',
                self::FIXTURES . '/somefile.txt.zip'
            );
    }

    public function testZipDecompressesGivenFile()
    {
        $result = $this
            ->zip
            ->decompress(
                self::FIXTURES . '/file.zip',
                self::FIXTURES . '/zip'
            );

        $this->assertTrue(true, $result);
        $this->assertFileExists(self::FIXTURES . '/zip/file.txt');

        // Cleanup
        $this->deleteRecursive(self::FIXTURES . '/zip');
    }

    public function testZipThrowsExceptionWhenDecompressesNotCompressedFile()
    {
        $this->expectException(\Compression\Exception\ZipException::class);
        $this->expectExceptionMessage("Zip: Unable to decompress file.txt not a valid zip file.");
        $this
            ->zip
            ->decompress(
                self::FIXTURES . '/file.txt',
                self::FIXTURES . '/file.txt'
            );
    }

    public function testZipCompressThrowsExceptionWhenFailedInternally()
    {
        $this->expectException(\Compression\Exception\ZipException::class);
        $this->expectExceptionMessage("Zip: Unable to compress file.txt.");
        $executorMock = $this->getMockBuilder(ExecutorInterface::class)
            ->setMethods(['execute'])
            ->getMock();
        $executorMock
            ->method('execute')
            ->willReturn(5);

        $zip = new Zip($executorMock);
        $zip->compress(
            self::FIXTURES . '/file.txt',
            self::FIXTURES . '/file.zip'
        );
    }

    public function testZipDecompressThrowsExceptionWhenFailedInternally()
    {
        $this->expectException(\Compression\Exception\ZipException::class);
        $this->expectExceptionMessage("Zip: Unable to decompress file file.zip.");
        $executorMock = $this->getMockBuilder(ExecutorInterface::class)
            ->setMethods(['execute'])
            ->getMock();
        $executorMock
            ->method('execute')
            ->willReturn(5);

        $zip = new Zip($executorMock);
        $zip->decompress(
            self::FIXTURES . '/file.zip',
            self::FIXTURES . '/file.zip'
        );
    }

    public function testPigzThrowsExceptionWhenTryToCompressDirectory()
    {
        $this->expectException(\Compression\Exception\ZipException::class);
        $this->expectExceptionMessage("Directory compression is not yet supported.");
        $this
            ->zip
            ->compress(
                self::FIXTURES,
                self::FIXTURES . '/compressed.zip'
            );
    }

    public function tearDown(): void
    {
        @unlink(self::FIXTURES . '/file.txt.gz');
        @unlink(self::FIXTURES . '/compressed.txt');
        @unlink(self::FIXTURES . '/file.txt.zip');
    }
}
