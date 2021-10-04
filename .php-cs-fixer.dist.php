<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('app')
    ->exclude('bin')
    ->exclude('var')
    ->exclude('tools')
;

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR12' => true,
        '@PSR1' => true,
        '@PSR4' => true,
        '@Symfony' => true
    ])
    ->setFinder($finder)
;