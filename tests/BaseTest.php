<?php

namespace Compression;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    public const FIXTURES = __DIR__ . '/Fixtures';
    public const TMPDIR = '/tmp';

    protected function deleteRecursive($directory) {
        $files = array_diff(
            @scandir($directory), ['.', '..']
        );

        foreach ($files as $file) {
            (is_dir("$directory/$file"))
                ? $this->deleteRecursive("$directory/$file")
                : @unlink("$directory/$file");
        }

        return rmdir($directory);
    }
}
