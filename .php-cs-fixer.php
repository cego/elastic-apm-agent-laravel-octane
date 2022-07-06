<?php

use Cego\CegoFixer;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src');

return CegoFixer::applyRules($finder);
