<?php
// src/config.php
function env($key, $default=null) {
  static $loaded = false;
  static $env = [];
  if (!$loaded) {
    $path = dirname(__DIR__) . '/.env';
    if (file_exists($path)) {
      foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line),'#')) continue;
        [$k,$v] = array_map('trim', explode('=', $line, 2));
        $env[$k] = $v;
      }
    }
    $loaded = true;
  }
  return $env[$key] ?? $default;
}

define('API_KEY', env('API_KEY', ''));
define('OPENAI_KEY', env('OPENAI_API_KEY',''));
define('OPENAI_MODEL', env('OPENAI_EMBED_MODEL','text-embedding-3-small'));
