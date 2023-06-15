<?php

declare(strict_types=1);

namespace Compression\Adapter;

use Compression\Contract\CompressionAdapterInterface;
use Compression\Contract\ExecutorInterface;
use Compression\Exception\PigzException;
use Compression\Lib\Passthru;

class Pigz implements CompressionAdapterInterface
{
    use CompressionAwareTrait;

    private ExecutorInterface $executor;
    private string $library = 'pigz';
    private array $mimetype = ['application/gzip', 'application/x-gzip'];
    private string $adapterException = PigzException::class;

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
            throw new PigzException(
                sprintf('Pigz: unable to compress, %s not available.', basename($source))
            );
        }
        /**
         * Pigz options info
         * -3 Compression level
         * -c Write all processed output to stdout (won't delete)
         * -f Force overwrite, compress .gz, links, and to terminal
         * -k Do not delete original file after processing
         * -q for quite mode so we don't see any.
         */
        $status = $this->executor->execute(
            sprintf(
                '%1$s -3cfkq %2$s > %3$s',
                $this->library,
                $this->escape($source),
                $this->escape($destination)
            )
        );
        if ($status !== 0) {
            throw new PigzException(
                sprintf('Pigz: Unable to compress file %s.', basename($source))
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function decompress(string $file, string $destination): bool
    {
        /**
         * Pigz options info
         * -d Decompress the compressed input
         * -f Force overwrite, compress .gz, links, and to terminal
         * -k Do not delete original file after processing
         * -q for quite mode so we don't see any.
         */
        $status = $this->executor->execute(
            sprintf(
                '%1$s -dfkq %2$s > %3$s',
                $this->library,
                $this->escape($file),
                $this->escape($destination)
            )
        );
        if ($status !== 0) {
            throw new PigzException(
                sprintf('Pigz: Unable to decompress file %s.', basename($file))
            );
        }

        return true;
    }
}
