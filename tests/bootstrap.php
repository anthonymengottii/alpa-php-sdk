<?php
/**
 * Bootstrap de testes — autoloader PSR-4 simples para Alpa\ e Alpa\Tests\.
 * Evita depender do dump de autoload do Composer durante o rebrand.
 */

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Alpa\\Tests\\' => __DIR__ . '/',
        'Alpa\\'        => __DIR__ . '/../src/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($class, $prefix, $len) !== 0) {
            continue;
        }
        $relative = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});
