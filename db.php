<?php
// 1. Database Credentials
$host = 'ep-shy-heart-alnkzh60-pooler.c-3.eu-central-1.aws.neon.tech';
$db   = 'neondb';
$user = 'neondb_owner';
$pass = 'npg_O69odUBLsJvg';
$endpoint = 'ep-shy-heart-alnkzh60';

try {
    // 2. The DSN must be clean
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require";

    // 3. The Password trick for Neon Pooler:
    // We combine the endpoint and the password with a semicolon.
    // This tells the pooler: "Route me to this endpoint, then check this password."
    $final_password = "endpoint=$endpoint;$pass";

    $pdo = new PDO($dsn, $user, $final_password);
    
    // Set error mode so we can see if the INSERT fails later
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // If this fails, it will tell us why (e.g., Password failed or Host not found)
    die("Connection failed: " . $e->getMessage());
}
?>