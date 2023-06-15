<?php

declare(strict_types=1);

namespace Compression\Adapter;

use Compression\Contract\CompressionAdapterInterface;
use Compression\Contract\ExecutorInterface;
use Compression\Exception\ZipException;
use Compression\Lib\Passthru;

class Zip implements CompressionAdapterInterface
{
    use CompressionAwareTrait;

    private ExecutorInterface $executor;
    private string $zipLibrary = 'zip';
    private string $unzipLibrary = 'unzip';
    private array $mimetype = ['application/zip'];
    private string $adapterException = ZipException::class;

    /**
     * @param ExecutorInterface|null $executor
     */
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
            throw new ZipException(
                sprintf('Zip: unable to compress, %s not available.', basename($source))
            );
        }
        /**
         * Zip options info
         * -j junk (don't record) directory names.
         */
        $status = $this->executor->execute(
            sprintf(
                '%1$s -jqq %2$s %3$s',
                $this->zipLibrary,
                $this->escape($destination),
                $this->escape($source)
            )
        );
        if ($status !== 0) {
            throw new ZipException(
                sprintf(
                    'Zip: Unable to compress %s.',
                    basename($source)
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     * @throws \Compression\Exception\ZipException
     */
    public function decompress(string $file, string $destination): bool
    {
        if (!$this->isCompressed($file)) {
            throw new ZipException(
                sprintf(
                    'Zip: Unable to decompress %s not a valid zip file.',
                    basename($file)
                )
            );
        }

        /**
         * Zip options info
         * -o overwrite files WITHOUT prompting
         * -j junk paths (do not make directories)
         * -q quiet mode
         * -d extract files into exdir.
         */
        $status = $this->executor->execute(
            sprintf(
                '%1$s -ojq -d %2$s %3$s',
                $this->unzipLibrary,
                $this->escape($destination),
                $this->escape($file)
            )
        );
        if ($status !== 0) {
            throw new ZipException(
                sprintf(
                    'Zip: Unable to decompress file %s.',
                    basename($file)
                )
            );
        }

        return true;
    }
}
