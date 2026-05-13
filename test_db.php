<?php
// Quick test - just check if MySQL is accessible at all
$hosts = ['localhost', '127.0.0.1', '::1'];

foreach ($hosts as $host) {
    $sock = @fsockopen($host, 3306, $errno, $errstr, 2);
    if ($sock) {
        echo "✓ Can reach MySQL at $host:3306\n";
        fclose($sock);
    }
}

// Try direct PDO connection approaches
echo "\n--- Testing PDO Connections ---\n";

$dsns = [
    'mysql:dbname=mysql' => 'Without host (default socket)',
    'mysql:host=127.0.0.1;port=3306;dbname=mysql' => 'TCP 127.0.0.1:3306',
    'mysql:host=localhost;port=3306;dbname=mysql' => 'TCP localhost:3306',
];

foreach ($dsns as $dsn => $desc) {
    try {
        $pdo = new PDO($dsn, 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        echo "✓ SUCCESS: $desc\n";
        // Try to create database
        $pdo->exec('CREATE DATABASE IF NOT EXISTS job_portal_db');
        echo "  → Database created\n";
        break;
    } catch (Exception $e) {
        echo "✗ FAILED: $desc\n";
        echo "  Error: " . substr($e->getMessage(), 0, 80) . "\n";
    }
}
?>
