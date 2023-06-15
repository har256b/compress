<?php

declare(strict_types=1);

namespace Compression\Contract;

interface ExecutorInterface
{
    public function execute(string $command): int;
}
