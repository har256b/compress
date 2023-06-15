<?php

declare(strict_types=1);

namespace Compression\Adapter;

use Compression\Exception\CompressionAdapterException;

trait CompressionAwareTrait
{
    /**
     * {@inheritdoc}
     */
    public function isCompressed(string $file): bool
    {
        return in_array($this->getMimeType($file), $this->mimetype);
    }

    /**
     * Shorthand for internal function 'escapeshellarg'.
     *
     * @param string $argument
     *
     * @return string
     */
    public function escape(string $argument): string
    {
        return escapeshellarg($argument);
    }

    /**
     * @param string $source
     *
     * @throws CompressionAdapterException
     *
     * @return bool
     */
    public function assertSourceValidation(string $source): bool
    {
        if (is_dir($source)) {
            throw new $this->adapterException('Directory compression is not yet supported.');
        }
        if (file_exists($source) && is_readable($source)) {
            return true;
        }

        return false;
    }

    /**
     * Detects given file's mimetype information.
     *
     * @param string $file
     *
     * @return string
     */
    private function getMimeType(string $file): string
    {
        $fileInfo = file_exists('/usr/share/misc/magic') ? finfo_open(FILEINFO_MIME_TYPE, '/usr/share/misc/magic') : finfo_open(FILEINFO_MIME_TYPE);
        $result = finfo_file($fileInfo, $file);
        finfo_close($fileInfo);

        if ($result === 'application/octet-stream') {
            // @codeCoverageIgnoreStart
            $command = 'file -b --mime-type ' . escapeshellarg($file);
            $result2 = trim(`$command`);

            return $result2 ?: $result;
            // @codeCoverageIgnoreEnd
        }

        return $result;
    }
}
