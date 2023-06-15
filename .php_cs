<?php

$config = new PhpCsFixer\Config();
$config
    ->getFinder()
    ->in(__DIR__ . '/src')
;

$config
    ->setRiskyAllowed(true)
    ->setCacheFile(__DIR__ . '/.php_cs.cache')
;

return $config;
