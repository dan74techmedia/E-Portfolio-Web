<?php

// Database configuration using DATABASE_URL from Neon
$dbUrl = getenv('DATABASE_URL');

if (!$dbUrl) {
    die('No database URL provided in the environment variables.');
}

try {
    $db = new PDO($dbUrl);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

return $db;
