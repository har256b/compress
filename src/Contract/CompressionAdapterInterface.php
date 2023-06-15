<?php

declare(strict_types=1);

namespace Compression\Contract;

use Compression\Exception\PigzException;

/**
 * Interface CompressionAdapterInterface.
 */
interface CompressionAdapterInterface
{
    /**
     * @param string $file
     *
     * @return bool
     */
    public function isCompressed(string $file): bool;

    /**
     * @param string $source      File/directory source
     * @param string $destination Destination of compressed file
     *
     * @throws PigzException
     *
     * @return bool
     */
    public function compress(string $source, string $destination): bool;

    /**
     * @param string $file        Compressed file path.
     * @param string $destination
     *
     * @throws PigzException
     *
     * @return bool
     */
    public function decompress(string $file, string $destination): bool;
}
