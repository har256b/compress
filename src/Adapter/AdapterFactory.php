<?php

declare(strict_types=1);

namespace Compression\Adapter;

use Compression\Contract\CompressionAdapterInterface;
use Compression\Exception\CompressionAdapterException;

final class AdapterFactory
{
    /**
     * @param string $file
     *
     * @throws CompressionAdapterException
     *
     * @return CompressionAdapterInterface
     */
    public static function createFromFile(string $file): CompressionAdapterInterface
    {
        $adapters = [
            Bz2::class, Targz::class, Rar::class, Zip::class,
        ];

        foreach ($adapters as $adapter) {
            $instance = new $adapter;
            if ($instance instanceof CompressionAdapterInterface && $instance->isCompressed($file)) {
                return $instance;
            }
        }

        throw new CompressionAdapterException('Unsupported compression type.');
    }
}
