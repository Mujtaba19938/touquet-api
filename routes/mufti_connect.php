<?php
require_once __DIR__ . '/../src/util.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/WhatsAppClient.php';

$body = read_json();
$user_name = trim($body['name'] ?? 'User');
$question  = trim($body['question'] ?? '');
$madhhab   = trim($body['madhhab'] ?? '');
$lang      = trim($body['lang'] ?? '');

if (!$question) json_error('question required');

$pdo = db();
$mufti = $pdo->query("SELECT name, whatsapp FROM muftis WHERE is_active=1 ORDER BY id ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$mufti || !$mufti['whatsapp']) {
  // fallback to global number from .env
  $wa = env('WHATSAPP_NUMBER', '');
  if (!$wa) json_ok(['ok'=>false, 'message'=>'No Mufti available right now. Please try later.']);
  $link = WhatsAppClient::deepLink($wa, "Assalamualaikum, I need guidance.\nName: {$user_name}\nMadhhab: {$madhhab}\nLang: {$lang}\nQuestion: {$question}");
  json_ok(['ok'=>true, 'whatsapp_link'=>$link]);
} else {
  $link = WhatsAppClient::deepLink($mufti['whatsapp'], "Assalamualaikum Mufti Sahib,\nNew question:\nFrom: {$user_name}\nMadhhab: {$madhhab}\nLang: {$lang}\nQ: {$question}");
  json_ok(['ok'=>true, 'whatsapp_link'=>$link, 'mufti'=>$mufti['name']]);
}
