<?php

namespace Compression;

use Compression\Contract\CompressionAdapterInterface;
use Compression\Contract\CompressionInterface;
use Compression\Exception\CompressionException;
use Compression\Exception\PigzException;

class CompressionTest extends BaseTest
{
    /**
     * @var CompressionAdapterInterface
     */
    protected $adapter;

    /**
     * @var Compression
     */
    protected Compression $compression;

    public function setUp(): void
    {
        $this->adapter = $this->getMockBuilder(CompressionAdapterInterface::class)
            ->setMethods(['isCompressed', 'compress', 'decompress'])
            ->getMock();

        $this->compression = new Compression($this->adapter);
    }

    public function testCompressionObjectIsCreatedWithAdapter()
    {
        $compression = new Compression($this->adapter);

        $this->assertInstanceOf(
            CompressionInterface::class, $compression
        );
    }

    public function testGetAdapter()
    {
        $this->assertEquals($this->adapter, $this->compression->getAdapter());
    }

    public function testSetAdapter()
    {
        $newAdapter = $this->getMockBuilder(CompressionAdapterInterface::class)
            ->setMethods(['isCompressed', 'compress', 'decompress'])
            ->getMock();
        $this
            ->compression
            ->setAdapter($newAdapter);

        $this->assertEquals($newAdapter, $this->compression->getAdapter());
        $this->assertNotSame($this->adapter, $this->compression->getAdapter());
    }

    public function testIsCompressedFileTrue()
    {
        $this
            ->adapter
            ->method('isCompressed')
            ->willReturn(true);
        $this
            ->compression->
            setAdapter($this->adapter);

        $result = $this
            ->compression
            ->isCompressed(self::FIXTURES . '/compressed.txt.gz');

        $this->assertTrue($result);
    }

    public function testIsCompressedFileFalse()
    {
        $this
            ->adapter
            ->method('isCompressed')
            ->willReturn(false);
        $this
            ->compression
            ->setAdapter($this->adapter);

        $result = $this
            ->compression
            ->isCompressed(self::FIXTURES . '/compressed.txt');

        $this->assertFalse($result);
    }

    public function testCompressionThrowsExceptionWhenSourceNotExists()
    {
        $this->expectExceptionMessage("Unable to compress, file notExistingFile.txt not available.");
        $this->expectException(CompressionException::class);
        $this
            ->adapter
            ->method('compress')
            ->willThrowException(new PigzException);

        $this
            ->compression
            ->compress('notExistingFile.txt', '');
    }

    public function testCompressionThrowsExceptionWhenDestinationExistsAndForceIsFalse()
    {
        $this->expectExceptionMessage("Unable to compress, file compressed.txt.gz already exist.");
        $this->expectException(CompressionException::class);
        $this
            ->adapter
            ->method('compress')
            ->willThrowException(new PigzException);

        $this
            ->compression
            ->compress(
                self::FIXTURES . '/file.txt',
                self::FIXTURES . '/compressed.txt.gz',
                false
            );
    }

    public function testCompressionThrowsExceptionWhenAdapterFails()
    {
        $this->expectExceptionMessage("Unable to compress file.");
        $this->expectException(CompressionException::class);
        $this
            ->adapter
            ->method('compress')
            ->willThrowException(new PigzException(
                'Unable to compress file.'
            ));

        $this
            ->compression
            ->compress(
                self::FIXTURES . '/file.txt',
                self::FIXTURES . '/compressed.txt.gz',
                true
            );
    }

    public function testCompressionWhenDestinationExistsAndForceIsTrue()
    {
        $this
            ->adapter
            ->method('compress')
            ->willReturn(true);

        $result = $this->compression->compress(
            self::FIXTURES . '/file.txt',
            self::FIXTURES . '/force.compressed.txt.gz'
        );

        $this->assertTrue($result);
//        $this->assertFileExists(self::FIXTURES . '/force.compressed.txt.gz');
//        $this->assertEquals(7156, filesize(self::FIXTURES . '/force.compressed.txt.gz'));
    }

    public function testDecompressionThrowsExceptionWhenFileNotExists()
    {
        $this->expectExceptionMessage("Unable to decompress, file notExistingFile.txt is not readable.");
        $this->expectException(CompressionException::class);
        $this
            ->adapter
            ->method('decompress')
            ->willThrowException(new PigzException(
                'Unable to decompress, file notExistingFile.txt is not readable.'
            ));
        $this
            ->compression
            ->decompress('notExistingFile.txt', '');
    }

    public function testDecompressionThrowsExceptionWhenFileIsNotCompressed()
    {
        $this->expectExceptionMessage("Unable to decompress, file file.txt is not a compressed file.");
        $this->expectException(CompressionException::class);
        $this
            ->adapter
            ->method('decompress')
            ->willThrowException(new PigzException(
                'Unable to decompress, file file.txt is not a compressed file.'
            ));
        $this
            ->compression
            ->decompress(
                self::FIXTURES . '/file.txt',
                ''
            );
    }

    public function testDecompressionWhenFileIsCompressed()
    {
        $this
            ->adapter
            ->method('isCompressed')
            ->willReturn(true);
        $this
            ->adapter
            ->method('decompress')
            ->willReturn(true);

        $result = $this->compression->decompress(
            self::FIXTURES . '/compressed.txt.gz',
            self::FIXTURES . '/compressed.txt'
        );

        $this->assertTrue($result);
    }

    public function testCompressDirectory()
    {
        $this->expectExceptionMessage("Directory compression is not yet supported.");
        $this->expectException(CompressionException::class);
        $this
            ->compression
            ->compress(
                self::FIXTURES,
                'output.gz'
            );
    }
}
