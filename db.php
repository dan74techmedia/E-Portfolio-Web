<?php
$host = 'ep-shy-heart-alnkzh60-pooler.c-3.eu-central-1.aws.neon.tech';
$db   = 'neondb';
$user = 'neondb_owner';
$pass = 'npg_O69odUBLsJvg';
$endpoint = 'ep-shy-heart-alnkzh60';

try {
    // 1. Keep the DSN simple (No 'options' parameter here)
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require";

    // 2. THE SECRET SAUCE: Combine endpoint and password with a semicolon.
    // The Pooler reads this and knows exactly where to send your data.
    $final_password = "endpoint=$endpoint;$pass";

    $pdo = new PDO($dsn, $user, $final_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("The system is having trouble reaching the database. <br> Error: " . $e->getMessage());
}
?>