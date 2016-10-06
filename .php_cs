<?php
$finder = (new Symfony\CS\Finder)->in([
    __DIR__.'/src',
    __DIR__.'/tests',
]);

return (new Symfony\CS\Config)
    ->finder($finder)
    ->setUsingCache(true)
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers([
        '-psr0',
        'psr4',
        '-blankline_after_open_tag',
        '-concat_without_spaces',
        '-new_with_braces',
        '-no_empty_comment',
        '-phpdoc_annotation_without_dot',
        '-phpdoc_no_empty_return',
        '-phpdoc_var_without_name',
        '-pre_increment',
        '-single_blank_line_before_namespace',
        '-single_quote',
        '-ternary_spaces',
        '-unalign_double_arrow',
        'align_double_arrow',
        'mb_str_functions',
        'multiline_spaces_before_semicolon',
        'newline_after_open_tag',
        'no_useless_else',
        'ordered_use',
        'php_unit_construct',
        'php_unit_dedicate_assert',
        'phpdoc_order',
        'short_array_syntax',
        'strict_param',
    ]);
