<?php

declare(strict_types=1);

namespace Compression\Lib;

use Compression\Contract\ExecutorInterface;

final class Passthru implements ExecutorInterface
{
    public function execute(string $command): int
    {
        $status = 0;
        passthru($command, $status);

        return $status;
    }
}
