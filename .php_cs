<?php

$ignoreStorageFolder = function (\SplFileInfo $file) {
    if (strpos($file->getPath(), "storage")) {
        return false;
    }
};

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->filter($ignoreStorageFolder);

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->finder($finder);