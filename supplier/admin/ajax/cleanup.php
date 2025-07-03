<?php
// Clean up old notification files (older than 1 hour)
$tempDir = __DIR__ . '/../temp/';
$files = glob($tempDir . '*_update_*.txt');

foreach ($files as $file) {
    if (filemtime($file) < time() - 3600) { // 1 hour old
        unlink($file);
    }
}
?>
