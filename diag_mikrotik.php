<?php
$host = '164.77.124.182';
$port = 8728;
$user = 'claude';
$pass = 'pluton2026';

echo "1. Intentando fsockopen a {$host}:{$port} ...\n";
$t0 = microtime(true);
$sock = @fsockopen($host, $port, $errno, $errstr, 4);
$dt  = round((microtime(true) - $t0) * 1000);

if (!$sock) {
    echo "   FALLO ({$dt}ms) errno={$errno}: {$errstr}\n";
    exit;
}
echo "   Conectado en {$dt}ms\n";

stream_set_timeout($sock, 4);

// Login
$words = ['/login', "=name={$user}", "=password={$pass}"];
foreach ($words as $w) {
    $len = strlen($w);
    fwrite($sock, chr($len) . $w);
}
fwrite($sock, chr(0));
echo "2. Login enviado\n";

// Leer respuesta
$result = [];
while (true) {
    $b = @fread($sock, 1);
    if ($b === false || $b === '') { echo "   Timeout o EOF leyendo\n"; break; }
    $len = ord($b);
    if ($len === 0) {
        if (!empty($result)) { echo "   Sentence: " . implode(' | ', $result) . "\n"; }
        if (!empty($result) && in_array($result[0], ['!done', '!fatal', '!trap'])) break;
        $result = [];
        continue;
    }
    $word = @fread($sock, $len);
    $result[] = $word;
}

fclose($sock);
echo "3. Listo\n";
