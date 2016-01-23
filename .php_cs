<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in([__DIR__ . '/src', __DIR__ . '/config']);

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers([
        'short_array_syntax',
        'duplicate_semicolon',
        'return',
        'single_quote',
        'standardize_not_equal',
        'new_with_braces',
        'multiline_array_trailing_comma',
        'phpdoc_scalar',
        'remove_leading_slash_use',
        'blankline_after_open_tag',
        'single_blank_line_before_namespace',
        'trailing_spaces',
        '-psr0',
    ])
    ->setUsingCache(true)
    ->finder($finder);
