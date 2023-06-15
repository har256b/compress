<?php

namespace Compression\Adapter;

use Compression\BaseTest;
use Compression\Contract\CompressionAdapterInterface;
use Compression\Contract\ExecutorInterface;

class RarTest extends BaseTest
{
    use CompressionAwareTrait;

    /**
     * @var Rar;
     */
    private $rar;

    protected function setUp(): void
    {
        $this->rar = new Rar();
    }

    public function testRarIsInstantiatedAndIsValidAdapter()
    {
        $this->assertInstanceOf(CompressionAdapterInterface::class, $this->rar);
    }

    public function testIsCompressed()
    {
        $result = $this
            ->rar
            ->isCompressed(
                self::FIXTURES . '/file.rar'
            );

        $this->assertTrue($result);
    }

    public function testIsCompressedFalse()
    {
        $result = $this
            ->rar
            ->isCompressed(
                self::FIXTURES . '/file.txt'
            );

        $this->assertFalse($result);
    }

    public function testRarCompressesGivenFile()
    {
        $result = $this
            ->rar
            ->compress(
                self::FIXTURES . '/file.txt',
                self::FIXTURES . '/file.txt.rar'
            );

        $this->assertTrue(true, $result);
        $this->assertFileExists(self::FIXTURES . '/file.txt.rar');
    }

    public function testRarThrowsExceptionWhenCompressNotExistingFile()
    {
        $this->expectException(\Compression\Exception\RarException::class);
        $this
            ->rar
            ->compress(
                self::FIXTURES . '/somefile.txt',
                self::FIXTURES . '/somefile.txt.rar'
            );
    }

    public function testRarDecompressesGivenFile()
    {
        mkdir(self::FIXTURES . '/rar');
        $result = $this
            ->rar
            ->decompress(
                self::FIXTURES . '/file.rar',
                self::FIXTURES . '/rar/'
            );

        $this->assertTrue(true, $result);
        $this->assertFileExists(self::FIXTURES . '/rar/file.txt');

        // Cleanup
        $this->deleteRecursive(self::FIXTURES . '/rar');
    }

    public function testRarThrowsExceptionWhenDecompressesNotCompressedFile()
    {
        $this->expectException(\Compression\Exception\RarException::class);
        $this
            ->rar
            ->decompress(
                self::FIXTURES . '/file.txt',
                ''
            );
    }

    public function testRarCompressThrowsExceptionWhenFailedInternally()
    {
        $this->expectException(\Compression\Exception\RarException::class);
        $this->expectExceptionMessage("Rar: Unable to compress source file.txt.");
        $executorMock = $this->getMockBuilder(ExecutorInterface::class)
            ->setMethods(['execute'])
            ->getMock();
        $executorMock
            ->method('execute')
            ->willReturn(12);

        $rar = new Rar($executorMock);
        $rar->compress(
            self::FIXTURES . '/file.txt',
            self::FIXTURES . '/file.txt.rar'
        );
    }

    public function testPigzDecompressThrowsExceptionWhenFailedInternally()
    {
        $this->expectException(\Compression\Exception\RarException::class);
        $this->expectExceptionMessage("Rar: Unable to decompress file file.rar.");
        $executorMock = $this->getMockBuilder(ExecutorInterface::class)
            ->setMethods(['execute'])
            ->getMock();
        $executorMock
            ->method('execute')
            ->willReturn(12);

        $rar = new Rar($executorMock);
        $rar->decompress(
            self::FIXTURES . '/file.rar',
            self::FIXTURES . '/file'
        );
    }

    public function testRarThrowsExceptionWhenTryToCompressDirectory()
    {
        $this->expectException(\Compression\Exception\RarException::class);
        $this->expectExceptionMessage("Directory compression is not yet supported.");
        $this
            ->rar
            ->compress(
                self::FIXTURES,
                self::FIXTURES . '/compressed.rar'
            );
    }

    public function tearDown(): void
    {
        @unlink(self::FIXTURES . '/file.txt.rar');
        @unlink(self::FIXTURES . '/file');
        if (is_dir(self::FIXTURES . '/rar')) {
            $this->deleteRecursive(self::FIXTURES . '/rar');
        }
    }
}
