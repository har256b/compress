<?php

declare(strict_types=1);

namespace Compression\Adapter;

use Compression\Contract\CompressionAdapterInterface;
use Compression\Contract\ExecutorInterface;
use Compression\Exception\RarException;
use Compression\Lib\Passthru;

class Rar implements CompressionAdapterInterface
{
    use CompressionAwareTrait;

    private ExecutorInterface $executor;
    private string $rarLibrary = 'rar';
    private string $unrarLibrary = 'rar';
    private array $mimetype = ['application/x-rar'];
    private string $adapterException = RarException::class;

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
            throw new RarException(
                sprintf('Rar: unable to compress, %s not available.', basename($source))
            );
        }
        /**
         * Rar options info
         * a Command - Add files to archive.
         */
        $status = $this->executor->execute(
            sprintf(
                '%1$s a %2$s %3$s > /dev/null',
                $this->rarLibrary,
                $this->escape($destination),
                $this->escape($source)
            )
        );
        if ($status !== 0) {
            throw new RarException(
                sprintf(
                    'Rar: Unable to compress source %s.',
                    basename($source)
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     * @throws \Compression\Exception\RarException
     */
    public function decompress(string $file, string $destination): bool
    {
        /**
         * Rar options info
         * e Command - Extract files without archived paths.
         */
        $status = $this->executor->execute(
            sprintf(
                '%1$s e %2$s %3$s > /dev/null',
                $this->unrarLibrary,
                $this->escape($file),
                $this->escape($destination)
            )
        );
        if ($status !== 0) {
            throw new RarException(
                sprintf(
                    'Rar: Unable to decompress file %s.',
                    basename($file)
                )
            );
        }

        return true;
    }
}
