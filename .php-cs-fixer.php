<?php

$rules = [
    '@PSR12' => true,
    'concat_space' => ['spacing' => 'one'],
    'no_multiline_whitespace_around_double_arrow' => true,
    'no_empty_statement' => true,
    'include' => true,
    'no_alias_functions' => true,
    'no_trailing_comma_in_singleline' => ['elements' => ['arguments', 'array_destructuring', 'array', 'group_import']],
    'not_operator_with_successor_space' => true,
    'trailing_comma_in_multiline' => ['after_heredoc' => false, 'elements' => ['arrays']],
    'multiline_whitespace_before_semicolons' => true,
    'no_leading_namespace_whitespace' => true,
    'no_blank_lines_after_phpdoc' => true,
    'object_operator_without_whitespace' => true,
    'phpdoc_indent' => true,
    'phpdoc_inline_tag_normalizer' => true,
    'phpdoc_tag_type' => true,
    'general_phpdoc_tag_rename' => true,
    'phpdoc_no_access' => true,
    'phpdoc_no_package' => false,
    'phpdoc_scalar' => true,
    'phpdoc_summary' => true,
    'phpdoc_to_comment' => true,
    'phpdoc_trim' => true,
    'phpdoc_no_alias_tag' => ['replacements' => ['type' => 'var']],
    'phpdoc_var_without_name' => true,
    'no_extra_blank_lines' => ['tokens' => ['extra', 'use']],
    'self_accessor' => false,
    'array_syntax' => ['syntax' => 'short'],
    'echo_tag_syntax' => ['format' => 'long', 'long_function' => 'echo', 'shorten_simple_statements_only' => true],
    'single_quote' => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'cast_spaces' => ['space' => 'single'],
    'standardize_not_equals' => true,
    'trim_array_spaces' => true,
    'binary_operator_spaces' => ['operators' => ['|' => null, '=>' => 'single_space', '=' => 'single_space'], ],
    'unary_operator_spaces' => true,
    'no_unused_imports' => true,
    'mb_str_functions' => true,
    'combine_consecutive_unsets' => true,
    'dir_constant' => true,
    'type_declaration_spaces' => ['elements' => ['function', 'property']],
    'is_null' => true,
    'class_attributes_separation' => ['elements' => ['const' => 'one', 'method' => 'one', 'property' => 'one', 'trait_import' => 'none']],
    'modernize_types_casting' => true,
    'no_php4_constructor' => true,
    'no_short_bool_cast' => true,
    'no_useless_else' => true,
    'normalize_index_brace' => true,
    'phpdoc_no_useless_inheritdoc' => true,
    'pow_to_exponentiation' => true,
    'psr_autoloading' => true,
    'align_multiline_comment' => ['comment_type' => 'phpdocs_only'],
    'single_line_comment_style' => ['comment_types' => ['hash']],
    'no_null_property_initialization' => true,
    'non_printable_character' => false,
    'blank_line_before_statement' => true,
    'no_superfluous_elseif' => true,
    'method_chaining_indentation' => true,
    'php_unit_construct' => true,
    'array_indentation' => true,
    'ordered_class_elements' => true,
];

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

return (new PhpCsFixer\Config())
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setRules($rules)
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
