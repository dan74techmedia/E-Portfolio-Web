<?php
// HARDCODED SETTINGS (Most reliable for troubleshooting)
$host = 'ep-shy-heart-alnkzh60-pooler.c-3.eu-central-1.aws.neon.tech';
$db   = 'neondb';
$user = 'neondb_owner';
$pass = 'npg_O69odUBLsJvg'; // Your actual password
$endpoint = 'ep-shy-heart-alnkzh60';

try {
    // We combine the endpoint and password here—this is what Neon REQUIRES
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ];
    
    // The "Endpoint Trick": We put the endpoint ID inside the password string
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require";
    $final_pass = "endpoint=$endpoint;$pass";

    $pdo = new PDO($dsn, $user, $final_pass, $options);
} catch (PDOException $e) {
    die("The system is having trouble reaching the database. Error: " . $e->getMessage());
}
?>