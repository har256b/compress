<?php

declare(strict_types=1);

namespace Compression;

use Compression\Contract\CompressionAdapterInterface;
use Compression\Contract\CompressionInterface;
use Compression\Exception\CompressionException;
use Compression\Exception\PigzException;

class Compression implements CompressionInterface
{
    protected CompressionAdapterInterface $adapter;

    public function __construct(CompressionAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function setAdapter(CompressionAdapterInterface $adapter): void
    {
        $this->adapter = $adapter;
    }

    /**
     * @return CompressionAdapterInterface adapter
     */
    public function getAdapter(): CompressionAdapterInterface
    {
        return $this->adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function isCompressed(string $file): bool
    {
        return $this->adapter->isCompressed($file);
    }

    /**
     * {@inheritdoc}
     */
    public function compress(string $source, string $destination, bool $forceOverwrite = true): bool
    {
        if (!$this->assertSourceValidation($source)) {
            throw new CompressionException(
                sprintf('Unable to compress, file %s not available.', $source)
            );
        }
        if (!$forceOverwrite && file_exists($destination)) {
            throw new CompressionException(
                sprintf('Unable to compress, file %s already exist.', basename($destination))
            );
        }

        try {
            return $this->adapter->compress($source, $destination);
        } catch (PigzException $e) {
            throw new CompressionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function decompress(string $file, string $destination): bool
    {
        try {
            return $this->adapter->decompress($file, $destination);
        } catch (PigzException $e) {
            throw new CompressionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws \Compression\Exception\CompressionException
     */
    private function assertSourceValidation(string $source): bool
    {
        if (is_dir($source)) {
            throw new CompressionException('Directory compression is not yet supported.');
        }
        if (file_exists($source) && is_readable($source)) {
            return true;
        }

        return false;
    }
}
