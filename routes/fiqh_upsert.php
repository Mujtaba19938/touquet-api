<?php
require_once __DIR__ . '/../src/util.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/embedding.php';

require_api_key();
$body = read_json();

$book     = trim($body['book'] ?? '');
$chapter  = trim($body['chapter'] ?? '');
$lang     = $body['lang'] ?? 'ur';
$text     = trim($body['text'] ?? '');
$ref      = trim($body['ref'] ?? null);
$embedding= $body['embedding'] ?? null;  // optional precomputed

if (!$book || !$chapter || !$text) { json_error('Missing fields'); }

$pdo = db();
$pdo->beginTransaction();
try {
  // ensure book exists
  $stmt = $pdo->prepare("SELECT id FROM fiqh_books WHERE title=? LIMIT 1");
  $stmt->execute([$book]);
  $book_id = $stmt->fetchColumn();
  if (!$book_id) {
    $pdo->prepare("INSERT INTO fiqh_books (title) VALUES (?)")->execute([$book]);
    $book_id = (int)$pdo->lastInsertId();
  }

  // insert text
  $ins = $pdo->prepare("INSERT INTO fiqh_texts (book_id, chapter, ref_citation, lang, text) VALUES (?,?,?,?,?)");
  $ins->execute([$book_id, $chapter, $ref, $lang, $text]);
  $text_id = (int)$pdo->lastInsertId();

  // embedding
  if (!$embedding) {
    $embedding = openai_embed($text);
  }
  if (!is_array($embedding) || !count($embedding)) { throw new Exception('Embedding failed'); }
  $dim = count($embedding);
  $norm = l2norm($embedding);
  $vec_json = json_encode($embedding);

  $e = $pdo->prepare("INSERT INTO fiqh_embeddings (text_id, dim, vec_json, l2norm) VALUES (?,?,?,?)");
  $e->execute([$text_id, $dim, $vec_json, $norm]);

  $pdo->commit();
  json_ok(['ok'=>true, 'text_id'=>$text_id, 'book_id'=>$book_id, 'dim'=>$dim]);
} catch (Throwable $ex) {
  $pdo->rollBack();
  json_error('Upsert failed: '.$ex->getMessage(), 500);
}
