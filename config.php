<?php
session_start();

function mongo_manager() {
    static $manager = null;
    if ($manager === null) {
        if (!class_exists('MongoDB\Driver\Manager')) {
            die('MongoDB PHP driver is not installed. Please enable it in php.ini');
        }
        try {
            $manager = new MongoDB\Driver\Manager('mongodb://127.0.0.1:27017');
        } catch (Exception $e) {
            die('MongoDB connection failed: ' . $e->getMessage());
        }
    }
    return $manager;
}

function mongo_dbname() {
    return 'job_portal_db';
}

function mongo_namespace($collection) {
    return mongo_dbname() . '.' . $collection;
}

function mongo_object_id($value) {
    if ($value instanceof MongoDB\BSON\ObjectId) {
        return $value;
    }
    if (is_string($value) && preg_match('/^[a-f\d]{24}$/i', $value)) {
        return new MongoDB\BSON\ObjectId($value);
    }
    return $value;
}

function bson_to_array($value) {
    if ($value instanceof MongoDB\BSON\ObjectId) {
        return (string)$value;
    }
    if ($value instanceof MongoDB\BSON\UTCDateTime) {
        return $value->toDateTime()->format('Y-m-d H:i:s');
    }
    if (is_array($value)) {
        return array_map('bson_to_array', $value);
    }
    if (is_object($value)) {
        $array = [];
        foreach ($value as $key => $item) {
            $array[$key] = bson_to_array($item);
        }
        return $array;
    }
    return $value;
}

function mongo_find_one($collection, $filter = [], $options = []) {
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = mongo_manager()->executeQuery(mongo_namespace($collection), $query);
    $rows = $cursor->toArray();
    if (count($rows) === 0) {
        return null;
    }
    return bson_to_array($rows[0]);
}

function mongo_find($collection, $filter = [], $options = []) {
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = mongo_manager()->executeQuery(mongo_namespace($collection), $query);
    return array_map('bson_to_array', $cursor->toArray());
}

function mongo_insert_one($collection, $document) {
    $bulk = new MongoDB\Driver\BulkWrite();
    if (!isset($document['_id'])) {
        $document['_id'] = new MongoDB\BSON\ObjectId();
    }
    $bulk->insert($document);
    mongo_manager()->executeBulkWrite(mongo_namespace($collection), $bulk);
    return $document['_id'];
}

function mongo_update_one($collection, $filter, $update, $options = ['upsert' => false]) {
    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->update($filter, $update, ['multi' => false, 'upsert' => $options['upsert']]);
    mongo_manager()->executeBulkWrite(mongo_namespace($collection), $bulk);
}

function mongo_delete_one($collection, $filter) {
    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->delete($filter, ['limit' => 1]);
    mongo_manager()->executeBulkWrite(mongo_namespace($collection), $bulk);
}

function mongo_delete_many($collection, $filter) {
    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->delete($filter, ['limit' => 0]);
    mongo_manager()->executeBulkWrite(mongo_namespace($collection), $bulk);
}

function mongo_count($collection, $filter = []) {
    $command = new MongoDB\Driver\Command(['count' => $collection, 'query' => $filter]);
    $cursor = mongo_manager()->executeCommand(mongo_dbname(), $command);
    $result = current($cursor->toArray());
    return $result->n ?? 0;
}

function mongo_aggregate($collection, $pipeline) {
    $command = new MongoDB\Driver\Command(['aggregate' => $collection, 'pipeline' => $pipeline, 'cursor' => new stdClass()]);
    $cursor = mongo_manager()->executeCommand(mongo_dbname(), $command);
    return array_map('bson_to_array', $cursor->toArray());
}

function sanitize($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
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
function current_recruiter() {
    return $_SESSION['recruiter'] ?? null;
}

function require_recruiter_login() {
    if (!isset($_SESSION['recruiter'])) {
        header('Location: recruiter_index.php');
        exit;
    }
}
