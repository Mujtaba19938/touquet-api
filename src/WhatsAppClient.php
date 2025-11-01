<?php
// src/WhatsAppClient.php
class WhatsAppClient {
  public static function deepLink(string $number, string $text): string {
    $msg = urlencode($text);
    $num = preg_replace('/[^0-9+]/','',$number);
    return "https://wa.me/{$num}?text={$msg}";
  }
}
