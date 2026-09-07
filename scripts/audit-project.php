<?php
// Read-only inspection: does not dispatch routes or execute application actions.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$report = ['shadowed_routes' => [], 'duplicate_names' => [], 'missing_route_parameters' => [], 'empty_actions' => [], 'debug_stops' => [], 'duplicate_migration_classes' => []];
$names = [];
foreach ($app['router']->getRoutes() as $route) {
    if (strpos($route->getActionName(), '@') !== false) {
        list($class, $method) = explode('@', $route->getActionName());
        if (method_exists($class, $method)) {
            $reflection = new ReflectionMethod($class, $method);
            $lines = file($reflection->getFileName());
            $source = implode('', array_slice($lines, $reflection->getStartLine() - 1, $reflection->getEndLine() - $reflection->getStartLine() + 1));
            $clean = '';
            foreach (token_get_all('<?php ' . $source) as $token) {
                if (is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT, T_OPEN_TAG])) continue;
                $clean .= is_array($token) ? $token[1] : $token;
            }
            $bodyStart = strpos($clean, '{');
            if ($bodyStart !== false && trim(substr($clean, $bodyStart + 1, strrpos($clean, '}') - $bodyStart - 1)) === '') {
                $report['empty_actions'][] = [$route->uri(), $route->getActionName()];
            }
            $required = array_filter($reflection->getParameters(), function ($parameter) {
                $type = $parameter->getType();
                return !$parameter->isOptional() && (!$type || $type->isBuiltin());
            });
            if (count($required) > count($route->parameterNames())) $report['missing_route_parameters'][] = $route->uri();
        }
    }
    if ($route->getName()) $names[$route->getName()][] = $route->uri();
    $path = preg_replace('/\{[^}]+\}/', '1', $route->uri());
    foreach (array_diff($route->methods(), ['HEAD']) as $method) {
        try {
            $matched = $app['router']->getRoutes()->match(\Illuminate\Http\Request::create('/' . ltrim($path, '/'), $method));
            if ($matched->getActionName() !== $route->getActionName()) {
                $report['shadowed_routes'][] = [$method, $route->uri(), $route->getActionName(), $matched->getActionName()];
            }
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { /* parameter constraints can reject the example */ }
    }
}
foreach ($names as $name => $uris) if (count($uris) > 1) $report['duplicate_names'][$name] = $uris;
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../app'));
foreach ($files as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') continue;
    $tokens = token_get_all(file_get_contents($file->getPathname()));
    foreach ($tokens as $i => $token) {
        if (!is_array($token) || $token[0] !== T_STRING || !in_array(strtolower($token[1]), ['dd', 'dump'])) continue;
        $next = $i + 1;
        while (isset($tokens[$next]) && is_array($tokens[$next]) && $tokens[$next][0] === T_WHITESPACE) $next++;
        if (($tokens[$next] ?? null) === '(') $report['debug_stops'][] = [str_replace(__DIR__ . '/../', '', $file->getPathname()), $token[2], $token[1]];
    }
}
$migrationClasses = [];
foreach (glob(__DIR__ . '/../database/migrations/*.php') as $file) {
    if (preg_match('/class\s+(\w+)\s+extends\s+Migration/', file_get_contents($file), $match)) $migrationClasses[$match[1]][] = basename($file);
}
foreach ($migrationClasses as $class => $files) if (count($files) > 1) $report['duplicate_migration_classes'][$class] = $files;
echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
