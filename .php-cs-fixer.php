<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR2'                           => true,
        'no_blank_lines_after_phpdoc'     => true,
        'no_empty_phpdoc'                 => true,
        'no_unused_imports'               => true,
        'phpdoc_indent'                   => true,
        'phpdoc_scalar'                   => true,
        'phpdoc_trim'                     => true,
        'phpdoc_separation'               => true,
        'whitespace_after_comma_in_array' => true,
        'class_attributes_separation'     => [
            'elements' => [
                'method' => 'one',
            ],
        ],
        'not_operator_with_space'         => true,
        'no_extra_blank_lines'            => [
            'tokens' => ['extra'],
        ],
        'concat_space'                    => [
            'spacing' => 'one',
        ],
        'binary_operator_spaces'          => [
            'operators' => [
                '=>' => 'align_single_space_minimal',
            ],
        ],
        'blank_line_before_statement'     => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try', 'for', 'foreach', 'if', 'switch', 'do', 'while'],
        ],
        'ordered_imports'                 => [
            'sort_algorithm' => 'length',
        ],
    ])
    ->setFinder($finder);