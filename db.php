<?php
// 1. Core Connection Details (Secured for Render, with XAMPP fallbacks)
$host = getenv('DB_HOST') ?: 'ep-shy-heart-alnkzh60-pooler.c-3.eu-central-1.aws.neon.tech';
$port = getenv('DB_PORT') ?: 5432;
$dbname = getenv('DB_NAME') ?: 'neondb';
$user = getenv('DB_USER') ?: 'neondb_owner';
$endpoint_id = getenv('DB_ENDPOINT') ?: 'ep-shy-heart-alnkzh60';
$actual_password = getenv('DB_PASS') ?: 'npg_O69odUBLsJvg';

// The "Endpoint Fix" for Neon drivers
$pass = "endpoint=$endpoint_id;$actual_password";

// 2. The Crash-Proof Connection Logic
$max_retries = 3;
$retry_count = 0;
$pdo = null;

while ($retry_count < $max_retries) {
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Success!
        break; 
    } catch (PDOException $e) {
        $retry_count++;
        if ($retry_count >= $max_retries) {
            die("The system is having trouble reaching the database. Error: " . $e->getMessage());
        }
        sleep(1);
    }
}
?>