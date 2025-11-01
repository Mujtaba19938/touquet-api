<?php
// src/util.php
function json_ok($data = [], $code = 200) {
  http_response_code($code);
  header('Content-Type: application/json');
  echo json_encode($data);
  exit;
}
function json_error($message, $code = 400) {
  http_response_code($code);
  header('Content-Type: application/json');
  echo json_encode(['error' => $message]);
  exit;
}
function read_json() {
  $raw = file_get_contents('php://input');
  $j = json_decode($raw, true);
  return is_array($j) ? $j : [];
}
