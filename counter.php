<?php
// Einfacher Seitenaufruf-Zähler ohne Cookies und ohne Speicherung personenbezogener Daten.
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$file = __DIR__ . '/counter.txt';
$fp = @fopen($file, 'c+');
if (!$fp) { http_response_code(500); echo '{}'; exit; }

flock($fp, LOCK_EX);
$count = (int) trim((string) stream_get_contents($fp));

// Bekannte Bots nicht mitzählen
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (!preg_match('/bot|crawl|spider|slurp|curl|wget|headless/i', $ua)) {
    $count++;
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, (string) $count);
    fflush($fp);
}

flock($fp, LOCK_UN);
fclose($fp);
echo json_encode(['count' => $count]);
