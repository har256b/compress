# Compress

Library to ease the usage of compression, wrapper around multiple low level compression tools `pigz/zip/tar`
Simple library for archiving and un-archiving files using several compression utilities

## Support Compression Types
Following types are supported currently:
* Pigz
* Zip
* Targz
* Rar
* Bz2

## Usage
When using package for compressing file.
```php
$adapter = new Pigz();
$compressionService = new Compression($adapter);

$outoutFile = '/path/to/output/file';
$compressionService->compress($inputFile, $outputFile);
```

Detecting compression types when dealing with files directly from third parties. 
```php
$adapter = AdapterFactory::createFromFile('/path/to/compressed/file');
$compressionService = new Compression($adapter);

$outoutFile = '/path/to/output/file';
$compressionService->decompress($inputFile, $outputFile);
```

Checking if the file was compressed and has the valid extension

```php
$compressionService->isCompressed('/path/to/input/file');
```

## Install
`composer require har256b/compress`

## Dependencies
Following are the dependencies for package and should be available in the `$PATH` 
* `pigz` 
* `zip unzip`
* `rar unrar`
* `bzip2` 

## Tests/Coverage
```
$ ./vendor/bin/phpunit
$ composer coverage
```

## TODO
1. [ ] Add directory support to `Pigz`
2. [ ] Implement Zip adapter
3. [ ] Support for more custom arguments
4. [ ] Support for compression level selection
