<?php
session_start();

// Database configuration
const DB_HOST = '127.0.0.1';
const DB_NAME = 'job_portal_db';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

function db() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    return $pdo;
}

function query($sql, array $params = []) {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function fetch_one(string $sql, array $params = []) {
    $stmt = query($sql, $params);
    return $stmt->fetch() ?: null;
}

function fetch_all(string $sql, array $params = []) {
    return query($sql, $params)->fetchAll();
}

function execute(string $sql, array $params = []): int {
    return query($sql, $params)->rowCount();
}

function last_insert_id(): string {
    return db()->lastInsertId();
}

function begin_transaction(): void {
    db()->beginTransaction();
}

function commit_transaction(): void {
    db()->commit();
}

function rollback_transaction(): void {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
}

function sanitize($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

function current_recruiter() {
    return $_SESSION['recruiter'] ?? null;
}

function require_recruiter_login() {
    if (!isset($_SESSION['recruiter'])) {
        header('Location: recruiter_index.php');
        exit;
    }
}

function current_admin() {
    return $_SESSION['admin'] ?? null;
}

function require_admin_login() {
    if (!isset($_SESSION['admin'])) {
        header('Location: admin_index.php');
        exit;
    }
}

function get_flash() {
    if (!empty($_SESSION['flash'])) {
        $message = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $message;
    }
    return null;
}
