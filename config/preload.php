<?php

$directories = new DirectoryIterator(__DIR__.'/../var/cache/');
foreach ($directories as $directory) {
    if ($directory->isDir() && !$directory->isDot()) {
        $preloadFilesIterator = new RegexIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory->getPathname())
            ),
            '/App_Kernel.*\.preload\.php$/'
        );

        foreach ($preloadFilesIterator as $preloadFile) {
            require_once $preloadFile->getPathname();
        }
    }
}
