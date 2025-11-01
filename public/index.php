<?php
// public/index.php
require_once __DIR__ . '/../src/cors.php';
require_once __DIR__ . '/../src/util.php';

$uri = strtok($_SERVER['REQUEST_URI'], '?');
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
  'GET'     => [
    '/api/health' => __DIR__ . '/../routes/health.php',
  ],
  'POST'    => [
    '/api/fiqh/ask'     => __DIR__ . '/../routes/fiqh_ask.php',
    '/api/fiqh/upsert'  => __DIR__ . '/../routes/fiqh_upsert.php',
    '/api/mufti/connect'=> __DIR__ . '/../routes/mufti_connect.php',
  ],
  'OPTIONS' => [
    // wildcard preflight
  ]
];

if ($method === 'OPTIONS') {
  require_once __DIR__ . '/../routes/options.php';
  exit;
}

if (!isset($routes[$method][$uri])) {
  json_error('Not Found', 404);
}

require $routes[$method][$uri];
