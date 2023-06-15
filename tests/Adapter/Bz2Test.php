<?php

namespace Compression\Adapter;

use Compression\BaseTest;
use Compression\Contract\CompressionAdapterInterface;
use Compression\Contract\ExecutorInterface;

class Bz2Test extends BaseTest
{
    use CompressionAwareTrait;

    /**
     * @var Bz2;
     */
    private $bz2;

    protected function setUp(): void
    {
        $this->bz2 = new Bz2();
    }

    public function testPigzIsInstantiatedAndIsValidAdapter()
    {
        $this->assertInstanceOf(CompressionAdapterInterface::class, $this->bz2);
    }

    public function testIsCompressed()
    {
        $result = $this
            ->bz2
            ->isCompressed(
                self::FIXTURES . '/file.bz2'
            );

        $this->assertTrue($result);
    }

    public function testIsCompressedFalse()
    {
        $result = $this
            ->bz2
            ->isCompressed(
                self::FIXTURES . '/file.txt'
            );

        $this->assertFalse($result);
    }

    public function testBz2CompressesGivenFile()
    {
        $result = $this
            ->bz2
            ->compress(
                self::FIXTURES . '/file.txt',
                self::FIXTURES . '/file.txt.bz2'
            );

        $this->assertTrue(true, $result);
        $this->assertFileExists(self::FIXTURES . '/file.txt.bz2');
    }

    public function testBz2ThrowsExceptionWhenCompressNotExistingFile()
    {
        $this->expectException(\Compression\Exception\Bz2Exception::class);
        $this
            ->bz2
            ->compress(
                self::FIXTURES . '/somefile.txt',
                self::FIXTURES . '/somefile.txt.bz2'
            );
    }

    public function testBz2DecompressesGivenFile()
    {
        $result = $this
            ->bz2
            ->decompress(
                self::FIXTURES . '/file.bz2',
                self::FIXTURES . '/file'
            );

        $this->assertTrue(true, $result);
        $this->assertFileExists(self::FIXTURES . '/file');
    }

    public function testBz2ThrowsExceptionWhenDecompressesNotCompressedFile()
    {
        $this->expectException(\Compression\Exception\Bz2Exception::class);
        $this
            ->bz2
            ->decompress(
                self::FIXTURES . '/compressed.txt',
                self::FIXTURES . '/compressed.txt'
            );
    }

    public function testBz2CompressThrowsExceptionWhenFailedInternally()
    {
        $this->expectExceptionMessage("Bz2: Unable to compress source file.txt.");
        $this->expectException(\Compression\Exception\Bz2Exception::class);
        $executorMock = $this->getMockBuilder(ExecutorInterface::class)
            ->setMethods(['execute'])
            ->getMock();
        $executorMock
            ->method('execute')
            ->willReturn(12);

        $bz2 = new Bz2($executorMock);
        $bz2->compress(
            self::FIXTURES . '/file.txt',
            self::FIXTURES . '/file.txt.bz2'
        );
    }

    public function testPigzDecompressThrowsExceptionWhenFailedInternally()
    {
        $this->expectExceptionMessage("Bz2: Unable to decompress file file.bz2.");
        $this->expectException(\Compression\Exception\Bz2Exception::class);
        $executorMock = $this->getMockBuilder(ExecutorInterface::class)
            ->setMethods(['execute'])
            ->getMock();
        $executorMock
            ->method('execute')
            ->willReturn(12);

        $bz2 = new Bz2($executorMock);
        $bz2->decompress(
            self::FIXTURES . '/file.bz2',
            self::FIXTURES . '/file'
        );
    }

    public function testPigzThrowsExceptionWhenTryToCompressDirectory()
    {
        $this->expectExceptionMessage("Directory compression is not yet supported.");
        $this->expectException(\Compression\Exception\Bz2Exception::class);
        $this
            ->bz2
            ->compress(
                self::FIXTURES,
                self::FIXTURES . '/compressed.bz2'
            );
    }

    public function tearDown(): void
    {
        @unlink(self::FIXTURES . '/file.txt.bz2');
        @unlink(self::FIXTURES . '/file');
    }
}
