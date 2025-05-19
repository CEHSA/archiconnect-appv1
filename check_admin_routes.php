<?php
// Simple script to check which admin routes are defined in the application
// Save in the root of the Laravel project and run with 'php check_admin_routes.php'

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request to any page to bootstrap Laravel
$request = Illuminate\Http\Request::create('/');
$response = $kernel->handle($request);

// Get the router instance
$router = $app->make('router');
$routes = $router->getRoutes();

// Get all admin routes
$adminRoutes = [];
foreach ($routes as $route) {
    if (strpos($route->getName(), 'admin.') === 0) {
        $adminRoutes[] = [
            'name' => $route->getName(),
            'uri' => $route->uri(),
            'method' => implode('|', $route->methods()),
            'action' => $route->getActionName(),
        ];
    }
}

echo "Admin Routes:\n";
echo "=============\n\n";

foreach ($adminRoutes as $route) {
    echo "Name: " . $route['name'] . "\n";
    echo "URI: " . $route['uri'] . "\n";
    echo "Method: " . $route['method'] . "\n";
    echo "Action: " . $route['action'] . "\n";
    echo "--------------------------\n";
}

echo "\nTotal admin routes: " . count($adminRoutes) . "\n";
