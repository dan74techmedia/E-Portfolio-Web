<?php
// 1. Define the separate parts
$host = 'ep-shy-heart-alnkzh60-pooler.c-3.eu-central-1.aws.neon.tech';
$db   = 'neondb';
$user = 'neondb_owner';
$pass = 'npg_O69odUBLsJvg';
$endpoint = 'ep-shy-heart-alnkzh60';

try {
    // 2. The "Neon Secret": We combine endpoint and password into one string
    // This is the ONLY way the pooler recognizes your project
    $final_pass = "endpoint=$endpoint;$pass";
    
    // 3. Set up the connection with SSL required
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require";
    
    $pdo = new PDO($dsn, $user, $final_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // If it fails, this will tell us EXACTLY why
    echo "The system is having trouble reaching the database. <br>";
    echo "Error Details: " . $e->getMessage();
    exit;
}
?>