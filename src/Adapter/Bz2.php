<?php

declare(strict_types=1);

namespace Compression\Adapter;

use Compression\Contract\CompressionAdapterInterface;
use Compression\Contract\ExecutorInterface;
use Compression\Exception\Bz2Exception;
use Compression\Lib\Passthru;

class Bz2 implements CompressionAdapterInterface
{
    use CompressionAwareTrait;

    private ExecutorInterface $executor;

    private string $library = 'bzip2';

    private array $mimetype = ['application/x-bzip2'];

    /**
     * @var string
     */
    private string $adapterException = Bz2Exception::class;

    /**
     * @param ExecutorInterface|null $executor
     */
    public function __construct(ExecutorInterface $executor = null)
    {
        $this->executor = $executor ?: new Passthru;
    }

    /**
     * {@inheritdoc}
     * @throws \Compression\Exception\CompressionAdapterException
     */
    public function compress(string $source, string $destination): bool
    {
        if (!$this->assertSourceValidation($source)) {
            throw new Bz2Exception(
                sprintf('Bz2: unable to compress, %s not available.', basename($source))
            );
        }
        /**
         * Bzip2 options info
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
            throw new Bz2Exception(
                sprintf(
                    'Bz2: Unable to compress source %s.',
                    basename($source)
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     * @throws \Compression\Exception\Bz2Exception
     */
    public function decompress(string $file, string $destination): bool
    {
        /**
         * Bzip2 options info
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
            throw new Bz2Exception(
                sprintf(
                    'Bz2: Unable to decompress file %s.',
                    basename($file)
                )
            );
        }

        return true;
    }
}
