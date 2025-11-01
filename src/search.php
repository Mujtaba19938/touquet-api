<?php
// src/search.php
require_once __DIR__ . '/db.php';

function top_k_by_cosine(array $queryVec, int $k = 5, float $minScore = 0.82): array {
  // Pull candidates from DB, compute cosine in PHP (simple, OK for MVP)
  $pdo = db();
  $stmt = $pdo->query("SELECT fe.id, fe.text_id, fe.dim, fe.vec_json, ft.book_id, ft.chapter, ft.ref_citation, ft.lang, ft.text
                       FROM fiqh_embeddings fe
                       JOIN fiqh_texts ft ON ft.id=fe.text_id
                       LIMIT 2000"); // tune or paginate for large corpora

  $cands = [];
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $vec = json_decode($row['vec_json'], true);
    if (!is_array($vec)) continue;
    // cosine
    $score = 0.0;
    $len = min(count($vec), count($queryVec));
    $dot=0.0; $na=0.0; $nb=0.0;
    for($i=0;$i<$len;$i++){ $dot += $vec[$i]*$queryVec[$i]; $na+=$vec[$i]*$vec[$i]; $nb+=$queryVec[$i]*$queryVec[$i]; }
    if ($na>0 && $nb>0) $score = $dot / (sqrt($na)*sqrt($nb));
    if ($score >= $minScore) {
      $row['score'] = $score;
      $cands[] = $row;
    }
  }
  usort($cands, fn($a,$b)=> $b['score'] <=> $a['score']);
  return array_slice($cands, 0, $k);
}
