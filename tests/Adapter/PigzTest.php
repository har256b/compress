<?php

namespace Compression\Adapter;

use Compression\BaseTest;
use Compression\Contract\CompressionAdapterInterface;
use Compression\Contract\ExecutorInterface;

class PigzTest extends BaseTest
{
    use CompressionAwareTrait;

    /**
     * @var Pigz;
     */
    private $pigz;

    protected function setUp(): void
    {
        $this->pigz = new Pigz();
    }

    public function testPigzIsInstantiatedAndIsValidAdapter()
    {
        $this->assertInstanceOf(CompressionAdapterInterface::class, $this->pigz);
    }

    public function testIsCompressed()
    {
        $result = $this
            ->pigz
            ->isCompressed(
                self::FIXTURES . '/compressed.txt.gz'
            );

        $this->assertTrue($result);
    }

    public function testIsCompressedFalse()
    {
        $result = $this
            ->pigz
            ->isCompressed(
                self::FIXTURES . '/file.txt'
            );

        $this->assertFalse($result);
    }

    public function testPigzCompressesGivenFile()
    {
        $result = $this
            ->pigz
            ->compress(
                self::FIXTURES . '/file.txt',
                self::FIXTURES . '/file.txt.gz'
            );

        $this->assertTrue(true, $result);
        $this->assertFileExists(self::FIXTURES . '/file.txt.gz');
    }

    public function testPigzThrowsExceptionWhenCompressNotExistingFile()
    {
        $this->expectException(\Compression\Exception\PigzException::class);
        $this
            ->pigz
            ->compress(
                self::FIXTURES . '/somefile.txt',
                self::FIXTURES . '/somefile.txt.gz'
            );
    }

    public function testPigzDecompressesGivenFile()
    {
        $result = $this
            ->pigz
            ->decompress(
                self::FIXTURES . '/compressed.txt.gz',
                self::FIXTURES . '/compressed.txt'
            );

        $this->assertTrue(true, $result);
        $this->assertFileExists(self::FIXTURES . '/compressed.txt');
        $this->assertEquals(1090980, filesize(self::FIXTURES . '/compressed.txt'));
    }

    public function testPigzThrowsExceptionWhenDecompressesNotCompressedFile()
    {
        $this->expectExceptionMessage("Pigz: Unable to decompress file file.txt.");
        $this->expectException(\Compression\Exception\PigzException::class);
        $this
            ->pigz
            ->decompress(
                self::FIXTURES . '/file.txt',
                self::FIXTURES . '/file1.txt'
            );
    }

    public function testPigzCompressThrowsExceptionWhenFailedInternally()
    {
        $this->expectExceptionMessage("Pigz: Unable to compress file file.txt.");
        $this->expectException(\Compression\Exception\PigzException::class);
        $executorMock = $this->getMockBuilder(ExecutorInterface::class)
            ->setMethods(['execute'])
            ->getMock();
        $executorMock
            ->method('execute')
            ->willReturn(12);

        $pigz = new Pigz($executorMock);
        $pigz->compress(
            self::FIXTURES . '/file.txt',
            self::FIXTURES . '/file.gz'
        );
    }

    public function testPigzDecompressThrowsExceptionWhenFailedInternally()
    {
        $this->expectExceptionMessage("Pigz: Unable to decompress file file.txt.gz.");
        $this->expectException(\Compression\Exception\PigzException::class);
        $executorMock = $this->getMockBuilder(ExecutorInterface::class)
            ->setMethods(['execute'])
            ->getMock();
        $executorMock
            ->method('execute')
            ->willReturn(12);

        $pigz = new Pigz($executorMock);
        $pigz->decompress(
            self::FIXTURES . '/file.txt.gz',
            self::FIXTURES . '/file.txt'
        );
    }

    public function testPigzThrowsExceptionWhenTryToCompressDirectory()
    {
        $this->expectExceptionMessage("Directory compression is not yet supported.");
        $this->expectException(\Compression\Exception\PigzException::class);
        $this
            ->pigz
            ->compress(
                self::FIXTURES,
                self::FIXTURES . '/compressed.gz'
            );
    }

    public function tearDown(): void
    {
        @unlink(self::FIXTURES . '/file1.txt');
        @unlink(self::FIXTURES . '/file.txt.gz');
        @unlink(self::FIXTURES . '/compressed.txt');
        @unlink(self::FIXTURES . '/somefile.txt.gz');
    }
}
