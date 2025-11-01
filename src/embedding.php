<?php
// src/embedding.php
require_once __DIR__ . '/config.php';

function openai_embed(string $text): array {
  if (!OPENAI_KEY) { throw new Exception('OPENAI_API_KEY missing'); }

  $payload = [
    'input' => $text,
    'model' => OPENAI_MODEL,
  ];

  $ch = curl_init('https://api.openai.com/v1/embeddings');
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
      'Content-Type: application/json',
      'Authorization: Bearer ' . OPENAI_KEY
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
  ]);
  $resp = curl_exec($ch);
  if ($resp === false) throw new Exception('Embedding request failed');

  $data = json_decode($resp, true);
  return $data['data'][0]['embedding'] ?? [];
}

function l2norm(array $v): float {
  $s=0.0; foreach ($v as $x) { $s += $x*$x; } return sqrt($s);
}

function cosine(array $a, array $b): float {
  $dot=0.0; $na=0.0; $nb=0.0;
  $len = min(count($a), count($b));
  for($i=0;$i<$len;$i++){ $dot += $a[$i]*$b[$i]; $na += $a[$i]*$a[$i]; $nb += $b[$i]*$b[$i]; }
  if ($na<=0 || $nb<=0) return 0.0;
  return $dot / (sqrt($na)*sqrt($nb));
}
