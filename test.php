<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "chayseversunwin-production.up.railway.app";
$fp = fsockopen("ssl://$host", 443, $errno, $errstr, 10);

if (!$fp) {
    die("❌ Lỗi kết nối: $errno - $errstr");
} else {
    echo "✅ Kết nối thành công đến $host:443 qua SSL (fsockopen)";
    fclose($fp);
}
?>