<?php
require_once __DIR__ . '/../src/util.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/embedding.php';
require_once __DIR__ . '/../src/search.php';

$body = read_json();
$q = trim($body['question'] ?? '');
if (!$q) json_error('Question required');

try {
  $qvec = openai_embed($q);  // If you want pure offline vectors, precompute and ship; else keep this online.
} catch (Throwable $e) {
  // If embeddings unavailable, fail gracefully
  json_ok([
    'answer' => 'No answer found to this question. If you like, I can connect you to a Mufti.',
    'source' => null
  ]);
}

$top = top_k_by_cosine($qvec, 5, 0.82);
$pdo = db();

$topId = null; $topScore = null;
if (count($top)) {
  // Build a conservative, citation-first answer (no ijtihad)
  $best = $top[0];
  $topId = $best['text_id']; $topScore = $best['score'];

  // Compose short answer: quote + citation
  $answer = "According to {$best['chapter']} ({$best['lang']}) — {$best['text']}";
  $source = [
    'book_id' => (int)$best['book_id'],
    'chapter' => $best['chapter'],
    'ref'     => $best['ref_citation'],
    'score'   => round($best['score'], 4)
  ];

  $pdo->prepare("INSERT INTO queries (q, top_text_id, score) VALUES (?,?,?)")->execute([$q, $topId, $topScore]);
  json_ok(['answer' => $answer, 'source' => $source, 'alternates' => array_map(function($r){
    return [
      'chapter' => $r['chapter'],
      'ref'     => $r['ref_citation'],
      'score'   => round($r['score'],4),
      'excerpt' => mb_substr($r['text'], 0, 240) . (mb_strlen($r['text'])>240?'…':'')
    ];
  }, $top)]);
} else {
  $pdo->prepare("INSERT INTO queries (q) VALUES (?)")->execute([$q]);
  json_ok([
    'answer' => 'No answer found to this question. If you like, I can connect you to a Mufti.',
    'source' => null
  ]);
}
