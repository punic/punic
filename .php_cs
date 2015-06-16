<?php

return Symfony\CS\Config\Config::create()
    ->fixers(array(
        // Don't touch class/file name
        '-psr0',
        // Don't vertically align phpdoc tags
        '-phpdoc_params',
        // Allow 'return null'
        '-empty_return',
    ))
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->exclude(array('vendor', 'temp', 'code'.DIRECTORY_SEPARATOR.'data'))
            ->in(__DIR__)
        )
;
