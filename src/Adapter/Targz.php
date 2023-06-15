<?php

declare(strict_types=1);

namespace Compression\Adapter;

use Compression\Contract\CompressionAdapterInterface;
use Compression\Contract\ExecutorInterface;
use Compression\Exception\TargzException;
use Compression\Lib\Passthru;

class Targz implements CompressionAdapterInterface
{
    use CompressionAwareTrait;

    private ExecutorInterface $executor;
    private string $library = 'tar';
    private array $mimetype = ['application/gzip', 'application/x-gzip'];
    private string $adapterException = TargzException::class;

    public function __construct(ExecutorInterface $executor = null)
    {
        $this->executor = $executor ?: new Passthru();
    }

    /**
     * {@inheritdoc}
     * @throws \Compression\Exception\CompressionAdapterException
     */
    public function compress(string $source, string $destination): bool
    {
        if (!$this->assertSourceValidation($source)) {
            throw new TargzException(
                sprintf('Targz: unable to compress, %s not available.', basename($source))
            );
        }
        /**
         * Tar options info
         * -c Create mode
         * -z Create archive with gzip/bzip2/xz/lzma
         * -P don't strip leading '/'s from file names
         * -f Location of archive.
         */
        $status = $this->executor->execute(
            sprintf(
                '%1$s -czPf %2$s %3$s',
                $this->library,
                $this->escape($destination),
                $this->escape($source)
            )
        );
        if ($status !== 0) {
            throw new TargzException(
                sprintf(
                    'Tar: Unable to compress source %s.',
                    basename($source)
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     * @throws \Compression\Exception\TargzException
     */
    public function decompress(string $file, string $destination): bool
    {
        if (!$this->isCompressed($file)) {
            throw new TargzException(
                sprintf(
                    'Unable to decompress, %s is not a valid tar archive.',
                    basename($file)
                )
            );
        }
        /**
         * Tar options info
         * -x Extract mode
         * -z Create archive with gzip/bzip2/xz/lzma
         * -v Verbose
         * -f Location of archive
         * -C Change to <dir> before processing remaining files.
         */
        $status = $this->executor->execute(
            sprintf(
                '%1$s xzPf %2$s -C %3$s',
                $this->library,
                $this->escape($file),
                $this->escape($destination)
            )
        );
        if ($status !== 0) {
            throw new TargzException(
                sprintf(
                    'Tar: Unable to decompress file %s.',
                    basename($file)
                )
            );
        }

        return true;
    }
}
