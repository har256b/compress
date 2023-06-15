<?php

declare(strict_types=1);

namespace Compression\Contract;

use Compression\Exception\CompressionException;

/**
 * CompressionInterface defines the minimum contract for lib-compression.
 * Any implementation must adhere to the concerns.
 *
 * Interface CompressionInterface
 */
interface CompressionInterface
{
    /**
     * @param string $file
     *
     * @return bool
     */
    public function isCompressed(string $file): bool;

    /**
     * @param string $source         File/directory source
     * @param string $destination    Destination of compressed file
     * @param bool   $forceOverwrite Force overwrite if file already exist
     *
     * @throws CompressionException
     *
     * @return bool
     */
    public function compress(string $source, string $destination, bool $forceOverwrite = true): bool;

    /**
     * @param string $file        Compressed file path.
     * @param string $destination
     *
     * @throws CompressionException
     *
     * @return bool
     */
    public function decompress(string $file, string $destination): bool;
}
