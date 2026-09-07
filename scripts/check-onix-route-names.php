<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$missing = [];
foreach (['app', 'resources/views'] as $dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../' . $dir));
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') continue;
        preg_match_all('/\broute\(\s*[\'"]([\w.\-]+)[\'"]/', file_get_contents($file->getPathname()), $matches);
        foreach ($matches[1] as $name) {
            if (!$app['router']->has($name)) $missing[$name][] = str_replace(__DIR__ . '/../', '', $file->getPathname());
        }
    }
}
echo json_encode($missing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
