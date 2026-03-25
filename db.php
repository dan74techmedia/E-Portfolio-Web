<?php

$dbUrl = getenv('DATABASE_URL');

if (!$dbUrl) {
    die('No database URL provided in the environment variables.');
}

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    
    // For Neon: Use sslmode=require with proper certificate handling
    if (strpos($dbUrl, '?') === false) {
        $dbUrl .= '?sslmode=require';
    }
    
    $db = new PDO($dbUrl, null, null, $options);
    
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

return $db;
?>
