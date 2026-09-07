<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$missing = [];
foreach ($app['router']->getRoutes() as $route) {
    $action = $route->getActionName();
    if (strpos($action, '@') === false) continue;
    list($class, $method) = explode('@', $action);
    if (!class_exists($class) || !method_exists($class, $method)) {
        $missing[] = [$route->uri(), $action];
    }
}
echo json_encode($missing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
