<?php

$ignoreRoutesFile = function (\SplFileInfo $file) {
    return ($file->getFilename() == "routes.php") ? false : true;
};

$ignoreStorageFolder = function (\SplFileInfo $file) {
    if (strpos($file->getPath(), "storage") || strpos($file->getPath(), "bootstrap")) {
        return false;
    }
};

$ignoreVendorFolder = function (\SplFileInfo $file) {
    if (strpos($file->getPath(), "vendor") {
        return false;
    }
};

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->filter($ignoreRoutesFile)
    ->filter($ignoreStorageFolder)
    ->filter($ignoreVendorFolder);

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->finder($finder);