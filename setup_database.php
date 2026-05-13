<?php
/**
 * Database Setup Script
 * Run this to initialize the database
 */

// Try different connection methods
$connections = [
    ['host' => 'localhost', 'unix_socket' => '/var/run/mysqld/mysqld.sock'],
    ['host' => '127.0.0.1'],
    ['host' => '127.0.0.1', 'port' => 3306],
    ['host' => 'localhost', 'port' => 3306],
];

$pdo = null;
$connected_via = null;

foreach ($connections as $config) {
    try {
        $dsn = 'mysql:';
        $dsn_parts = [];
        
        if (isset($config['unix_socket'])) {
            $dsn_parts[] = 'unix_socket=' . $config['unix_socket'];
        }
        if (isset($config['host'])) {
            $dsn_parts[] = 'host=' . $config['host'];
        }
        if (isset($config['port'])) {
            $dsn_parts[] = 'port=' . $config['port'];
        }
        
        $dsn .= implode(';', $dsn_parts);
        
        $pdo = new PDO($dsn, 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        
        $connected_via = $config;
        echo "✓ Connected via: " . json_encode($config) . "\n";
        break;
    } catch (PDOException $e) {
        echo "✗ Failed with " . json_encode($config) . ": " . $e->getMessage() . "\n";
    }
}

if (!$pdo) {
    die("Could not connect to MySQL with any method.\n");
}

// Read schema
$schema = file_get_contents('schema.sql');

// Split by statements and execute
$statements = array_filter(
    array_map('trim', explode(';', $schema)),
    function($stmt) {
        return !empty($stmt) && strpos($stmt, '--') !== 0;
    }
);

foreach ($statements as $statement) {
    try {
        $pdo->exec($statement);
        echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
    } catch (PDOException $e) {
        echo "✗ Failed: " . $e->getMessage() . "\n";
    }
}

// Now seed the data
echo "\nSeeding data...\n";
require 'seed_mysql.php';

echo "\n✓ Database setup complete!\n";
?>
