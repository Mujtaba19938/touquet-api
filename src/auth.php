<?php
// src/auth.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util.php';

function require_api_key() {
  $sent = $_SERVER['HTTP_X_API_KEY'] ?? '';
  if (!$sent || $sent !== API_KEY) {
    json_error('Unauthorized', 401);
    exit;
  }
}
