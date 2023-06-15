* FileSystem Interface with methods (getPath)
* Create File/Directory object 
* Update Compression/Adapters to use new objects instead of string
* Add validation inside the FileSystem objects
* * Throw exception for each case i.e. Readable/Writable/Exists etc.
* Add remaining adapters


* Factory to implement all adapetrs

$a = AdapterFactory::createFromFile('file.targz');
$c = new Compression($a);