<?php

namespace Compression\Adapter;

use Compression\BaseTest;
use Compression\Contract\CompressionAdapterInterface;

class AdapterFactoryTest extends BaseTest
{
    use CompressionAwareTrait;

    protected function setUp(): void
    {
    }

    /**
     * @dataProvider adaptersProvider
     * @param $class
     * @param $file
     * @throws \Compression\Exception\CompressionAdapterException
     */
    public function testValidAdapterIsCreated($class, $file)
    {
        $adapterInstance = AdapterFactory::createFromFile(self::FIXTURES . '/' . $file);
        $this->assertInstanceOf($class, $adapterInstance);
    }

    public function adaptersProvider(): array
    {
        return [
            [Bz2::class, 'file.bz2'],
            [Targz::class, 'archive.tar.gz'],
            [Rar::class, 'file.rar'],
            [Zip::class, 'file.zip'],
        ];
    }

    public function testInvalidAdapterExceptionThrown()
    {
        $this->expectExceptionMessage("Unsupported compression type.");
        $this->expectException(\Compression\Exception\CompressionAdapterException::class);
        AdapterFactory::createFromFile(self::FIXTURES . '/file.txt');
    }
}
